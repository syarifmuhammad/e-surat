<?php

namespace App\Http\Controllers;

use App\Http\Resources\SuratKeputusanPemberhentianDanPengangkatanCollection as ThisCollection;
use App\Http\Resources\SuratKeputusanPemberhentianDanPengangkatanResource as ThisResource;
use App\Models\Approval;
use App\Models\ReferenceNumberSetting;
use App\Models\SuratKeputusanPemberhentianDanPengangkatan as Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Crypt;

class SuratKeputusanPemberhentianDanPengangkatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $letters = Letter::with(['signers', 'signers.employee', 'approvals', 'approvals.employee'])->search($request->search)->whereUser(auth()->user())->orderBy('is_signed')->orderBy('reference_number')->orderBy('id')->paginate();
        return new ThisCollection($letters);
    }

    public function graph_in_months($year)
    {
        if (!$year || $year === '') {
            $year = Carbon::now()->year;
        }

        $letters = Letter::select(DB::raw('MONTH(tanggal_surat) as month'), DB::raw('COUNT(*) as total'))
            ->byUser()
            ->whereYear('tanggal_surat', $year)
            ->groupBy(DB::raw('MONTH(tanggal_surat)'))
            ->get();

        $data = array_fill(0, 12, 0);
        foreach ($letters as $letter) {
            $data[$letter->month - 1] = $letter->total;
        }
        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'letter_template_id' => 'required|exists:letter_templates,id',
            'tanggal_surat' => 'required|date',
            'masa_berlaku.year' => 'sometimes|integer',
            'masa_berlaku.month' => 'sometimes|integer',
            'masa_berlaku.day' => 'sometimes|integer',
            'nomor_berita_acara' => "required|string",
            'tanggal_berita_acara' => "required|date",
            'employee.id' => 'required|exists:employees,id',
            'pemberhentian_dalam_jabatan' => 'required|string',
            'pengangkatan_dalam_jabatan' => 'required|string',
            'signers' => 'required|array|min:1',
            'approvals' => 'sometimes|array',
            'signature_type' => 'required|in:manual,digital,gambar tanda tangan',
        ]);

        if ($validate->fails()) {
            $response = [
                'errors' => $validate->errors(),
                'message' => "Validasi form gagal !"
            ];
            return response()->json($response, 422);
        }

        DB::beginTransaction();

        try {
            $letter = new Letter;
            $letter->letter_template_id = $request->letter_template_id;
            $letter->tanggal_surat = $request->tanggal_surat;
            $letter->masa_berlaku = to_interval($request->masa_berlaku['year'], $request->masa_berlaku['month'], $request->masa_berlaku['day']);
            $letter->nomor_berita_acara = $request->nomor_berita_acara;
            $letter->tanggal_berita_acara = $request->tanggal_berita_acara;
            $letter->employee_id = $request->employee['id'];
            $letter->pemberhentian_dalam_jabatan = $request->pemberhentian_dalam_jabatan;
            $letter->pengangkatan_dalam_jabatan = $request->pengangkatan_dalam_jabatan;
            $letter->signature_type = $request->signature_type;
            $letter->created_by = auth()->user()->id;
            $letter->save();

            Approval::create_approvals_and_signers_to_letter($letter, $request->signers, $request->approvals);

            if (count($request->approvals) == 0) {
                $letter->is_approved = true;
                $letter->save();
            }

            DB::commit();

            $response = [
                'message' => "Berhasil membuat surat keputusan pemberhentian dan pengangkatan dalam jabatan !"
            ];
            return response()->json($response, 201);
        } catch (\Exception $e) {
            DB::rollback();
            $response = [
                'errors' => $e->getMessage(),
                'message' => "Gagal membuat surat keputusan pemberhentian dan pengangkatan dalam jabatan !"
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $letter = Letter::find($id);
        if (!$letter) {
            return response()->json([
                'message' => "Surat keputusan pemberhentian dan pengangkatan dalam jabatan tidak ditemukan !"
            ], 404);
        }

        return response()->json([
            'data' => new ThisResource($letter)
        ], 200);
    }

    public function download_docx(string $id)
    {
        $letter = Letter::find($id);
        if (!$letter) {
            return response()->json([
                'message' => "Surat keputusan dan pengangkatan dalam jabatan tidak ditemukan !"
            ], 404);
        }

        $filename = $letter->id . '.docx';
        $fileNameServerDocx = "app/tmp/surat_keputusan_pemberhentian_dan_pengangkatan/" . $filename;
        $templateProcessor = $letter->generate_docx();
        $templateProcessor->setValue('tanda_tangan', '');
        $templateProcessor->saveAs(storage_path($fileNameServerDocx));
        return response()->download(storage_path($fileNameServerDocx), $filename)->deleteFileAfterSend();
    }

    public function download_pdf(string $id)
    {
        $letter = Letter::find($id);
        if (!$letter) {
            return response()->json([
                'message' => "Surat keputusan pemberhentian dan pengangkatan dalam jabatan tidak ditemukan !"
            ], 404);
        }

        if ($letter->signed_file != null && $letter->signature_type == "manual") {
            $fileNameServerPdf = 'app/signed_files/' . $letter->signed_file;
            return response()->download(storage_path($fileNameServerPdf), $letter->signed_file);
        }

        $fileNameServerDocx = "app/tmp/surat_keputusan_pemberhentian_dan_pengangkatan/" . $letter->id . '.docx';
        $templateProcessor = $letter->generate_docx();
        $templateProcessor->saveAs(storage_path($fileNameServerDocx));

        $response = Http::post(env('APP_DOCX_CONVERTER_URL') . '/convert', ['file_path' => $fileNameServerDocx]);
        if ($response->failed()) {
            return response()->json([
                'errors' => $response->json(),
            ], 500);
        }

        $filename = $letter->id . '.pdf';
        $tmpFileNameServerPdf = 'app/tmp/surat_keputusan_pemberhentian_dan_pengangkatan/' . $filename;
        if ($response->successful() && file_exists(storage_path($tmpFileNameServerPdf))) {
            unlink(storage_path($fileNameServerDocx));
            return response()->download(storage_path($tmpFileNameServerPdf), $filename);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $letter = Letter::find($id);
        if (!$letter) {
            return response()->json([
                'message' => "Surat keputusan pemberhentian dan pengangkatan dalam jabatan tidak ditemukan !"
            ], 404);
        }

        if (!$letter->can_edit()) {
            return response()->json([
                'message' => "Surat keputusan pemberhentian dan pengangkatan dalam jabatan tidak dapat diedit !"
            ], 403);
        }

        $validate = Validator::make($request->all(), [
            'letter_template_id' => 'required|exists:letter_templates,id',
            'tanggal_surat' => 'required|date',
            'masa_berlaku.year' => 'sometimes|integer',
            'masa_berlaku.month' => 'sometimes|integer',
            'masa_berlaku.day' => 'sometimes|integer',
            'nomor_berita_acara' => "required|string",
            'tanggal_berita_acara' => "required|date",
            'employee.id' => 'required|exists:employees,id',
            'pemberhentian_dalam_jabatan' => 'required|string',
            'pengangkatan_dalam_jabatan' => 'required|string',
            'signers' => 'required|array|min:1',
            'approvals' => 'sometimes|array',
            'signature_type' => 'required|in:manual,digital,gambar tanda tangan',
        ]);

        if ($validate->fails()) {
            $response = [
                'errors' => $validate->errors(),
                'message' => "Validasi form gagal !"
            ];
            return response()->json($response, 422);
        }

        DB::beginTransaction();

        try {
            $letter->letter_template_id = $request->letter_template_id;
            $letter->tanggal_surat = $request->tanggal_surat;
            $letter->masa_berlaku = to_interval($request->masa_berlaku['year'], $request->masa_berlaku['month'], $request->masa_berlaku['day']);
            $letter->nomor_berita_acara = $request->nomor_berita_acara;
            $letter->tanggal_berita_acara = $request->tanggal_berita_acara;
            $letter->employee_id = $request->employee['id'];
            $letter->pemberhentian_dalam_jabatan = $request->pemberhentian_dalam_jabatan;
            $letter->pengangkatan_dalam_jabatan = $request->pengangkatan_dalam_jabatan;
            $letter->signature_type = $request->signature_type;
            $letter->save();

            Approval::update_approvals_and_signers_to_letter($letter, $request->signers, $request->approvals);

            if (count($request->approvals) == 0) {
                $letter->is_approved = true;
            } else {
                $letter->is_approved = false;
            }
            $letter->save();

            DB::commit();

            $response = [
                'message' => "Berhasil mengubah surat keputusan pemberhentian dan pengangkatan dalam jabatan !"
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            DB::rollback();
            $response = [
                'errors' => $e->getMessage(),
                'message' => "Gagal mengubah surat keputusan pemberhentian dan pengangkatan dalam jabatan !"
            ];
            return response()->json($response, 500);
        }
    }

    public function upload_signed_file(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'signed_file' => 'required|file|mimes:pdf|max:10000',
        ]);

        if ($validate->fails()) {
            $response = [
                'errors' => $validate->errors(),
                'message' => "Validasi form gagal !"
            ];
            return response()->json($response, 422);
        }

        $letter = Letter::find($request->id);
        if (!$letter) {
            return response()->json([
                'message' => "Surat keputusan pemberhentian dan pengangkatan dalam jabatan tidak ditemukan !"
            ], 404);
        }

        if (!$letter->can_upload_verified_file()) {
            return response()->json([
                'message' => "Surat dengan jenis tanda tangan selain manual tidak dapat ditandatangani dengan upload file !"
            ], 403);
        }

        $file = $request->file('signed_file');
        $fileName = "surat_keputusan_pemberhentian_dan_pengangkatan_" . Str::replace("/", "-", $letter->get_reference_number()) . '.' . $file->getClientOriginalExtension();
        $fileNameServer = 'signed_files/' . $fileName;
        Storage::put($fileNameServer, file_get_contents($file));

        $letter->signed_file = $fileName;
        $letter->is_signed = true;
        $letter->save();

        $response = [
            'message' => "Berhasil mengupload file surat keputusan pemberhentian dan pengangkatan dalam jabatan !"
        ];
        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $letter = Letter::find($id);
        if (!$letter) {
            return response()->json([
                'message' => "Surat keputusan pemberhentian dan pengangkatan dalam jabatan tidak ditemukan !"
            ], 404);
        }

        if (!$letter->can_edit()) {
            return response()->json([
                'message' => "Surat keputusan pemberhentian dan pengangkatan dalam jabatan tidak dapat dihapus !"
            ], 403);
        }

        DB::beginTransaction();
        try {
            $old_file = $letter->id . '.pdf';
            $letter->approvals()->delete();
            $letter->signers()->delete();
            $letter->delete();
    
            $tmpFileNameServerPdf = 'app/tmp/surat_keputusan_pemberhentian_dan_pengangkatan/' . $old_file;
            if (file_exists(storage_path($tmpFileNameServerPdf))) {
                unlink(storage_path($tmpFileNameServerPdf));
            }
  
            DB::commit();
            
            $response = [
                'message' => "Berhasil menghapus surat keputusan pemberhentian dan pengangkatan dalam jabatan !"
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            $response = [
                'errors' => $e->getMessage(),
                'message' => "Gagal menghapus surat keputusan pemberhentian dan pengangkatan dalam jabatan !"
            ];
            return response()->json($response, 500);
        }
    }

    public function give_reference_number(Request $request)
    {
        $letter = Letter::find($request->id);
        if (!$letter) {
            return response()->json([
                'message' => "Surat keputusan pemberhentian dan pengangkatan dalam jabatan tidak ditemukan !"
            ], 404);
        }

        //get the latest reference_number
        $latest_number = Letter::where('reference_number', '!=', 'NULL')->whereMonth('tanggal_surat', Carbon::now()->month)->whereYear('tanggal_surat', Carbon::now()->year)->count();
        $letter->reference_number = ReferenceNumberSetting::get_and_parse_reference_number_with_date(Letter::NAME, $latest_number + 1, $letter->tanggal_surat);
        $letter->save();

        $response = [
            'message' => "Berhasil memberikan nomor surat !"
        ];

        return response()->json($response, 200);
    }

    public function approve(Request $request, $id)
    {
        $letter = Letter::find($id);

        if (!$letter) {
            return response()->json([
                'message' => "Surat keputusan pemberhentian dan pengangkatan dalam jabatan tidak ditemukan !"
            ], 404);
        }

        if (!$letter->can_approved()) {
            return response()->json([
                'message' => "Anda tidak dapat menyetujui surat ini !"
            ], 404);
        }

        DB::beginTransaction();
        try {
            $approval = $letter->approvals->where('employee_id', auth()->id())->first();
            $approval->is_approved = true;
            $approval->save();
            $letter->save();

            if ($letter->approvals->where('is_approved', false)->count() == 0) {
                $letter->is_approved = true;
                $letter->save();
            }

            DB::commit();
            return response()->json([
                'message' => "Berhasil menyetujui surat keputusan pemberhentian dan pengangkatan dalam jabatan !"
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => $e->getMessage(),
                'message' => "Gagal menyetujui surat keputusan pemberhentian dan pengangkatan dalam jabatan !"
            ], 500);
        }
    }

    public function sign(Request $request, $id)
    {
        $letter = Letter::find($id);

        if (!$letter) {
            return response()->json([
                'message' => "Surat keputusan pemberhentian dan pengangkatan dalam jabatan tidak ditemukan !"
            ], 404);
        }

        if (!$letter->can_signed()) {
            return response()->json([
                'message' => "Surat keputusan pemberhentian dan pengangkatan dalam jabatan tidak dapat ditanda tangani !"
            ], 404);
        }
        $fileName = null;
        try {
            if ($letter->signature_type == 'digital') {
                $payload = [
                    'letter' => $letter->id,
                    'letter_type' => 'surat_keputusan_pemberhentian_dan_pengangkatan',
                ];
                $encrypt_payload = Crypt::encrypt($payload);
                $url_confirmation = env('APP_FRONTEND_URL') . "/verify/$encrypt_payload";
                $fileName = "surat_keputusan_pemberhentian_dan_pengangkatan_" . Str::replace("/", "-", $letter->get_reference_number()) . '.png';
                QrCode::style('round')->format('png')->size(200)->generate($url_confirmation, storage_path('app/signed_files/' . $fileName));
            } else if ($letter->signature_type == 'gambar tanda tangan') {
                $signature = Employee::find(auth()->id())->signature;

                if (!$signature && !Storage::exists('signature/' . $signature)) {
                    return response()->json([
                        'message' => "Tanda tangan anda tidak ditemukan !"
                    ], 404);
                }

                $fileName = "surat_keputusan_pemberhentian_dan_pengangkatan_" . Str::replace("/", "-", $letter->get_reference_number()) . '.' . pathinfo(storage_path('signature/' . $signature), PATHINFO_EXTENSION);
                if (!Storage::copy('signature/' . $signature, 'signed_files/' . $fileName)) {
                    throw new \Exception("Gagal mengcopy gambar tanda tangan !");
                }
            }

            $signer = $letter->signers->where('employee_id', auth()->id())->first();
            $signer->signed_file = $fileName;
            $signer->is_approved = true;
            $signer->save();
            if ($letter->signers->where('is_approved', false)->count() == 0) {
                $letter->is_signed = true;
            }
            $letter->save();
            return response()->json([
                'message' => "Berhasil menandatangani surat keputusan pemberhentian dan pengangkatan dalam jabatan !"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
                'message' => "Gagal menandatangani surat keputusan pemberhentian dan pengangkatan dalam jabatan !"
            ], 500);
        }
    }
}
