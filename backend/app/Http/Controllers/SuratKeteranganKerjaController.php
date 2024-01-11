<?php

namespace App\Http\Controllers;

use App\Http\Resources\SuratKeteranganKerjaCollection as ThisCollection;
use App\Http\Resources\SuratKeteranganKerjaResource as ThisResource;
use App\Models\KeyPair;
use App\Models\ReferenceNumberSetting;
use App\Models\SuratKeteranganKerja as Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;

class SuratKeteranganKerjaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $letters = Letter::search($request->search)->whereUser(auth()->user())->paginate();
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
            'employee.id' => 'required|exists:employees,id',
            'employee.position' => 'required|string',
            'signer.id' => 'required|exists:employees,id',
            'signer.position' => 'required|string',
            'signature_type' => 'required|in:manual,digital,gambar tanda tangan',
        ]);
        if ($validate->fails()) {
            $response = [
                'errors' => $validate->errors(),
                'message' => "Validasi form gagal !"
            ];
            return response()->json($response, 422);
        }

        $letter = new Letter;
        $letter->letter_template_id = $request->letter_template_id;
        $letter->tanggal_surat = $request->tanggal_surat;
        $letter->employee_id = $request->employee['id'];
        $letter->position = $request->employee['position'];
        $letter->signer_id = $request->signer['id'];
        $letter->signer_position = $request->signer['position'];
        $letter->signature_type = $request->signature_type;
        $letter->created_by = auth()->user()->id;
        $letter->save();

        $response = [
            'message' => "Berhasil membuat surat keterangan kerja !"
        ];
        return response()->json($response, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $letter = Letter::find($id);
        if (!$letter) {
            return response()->json([
                'message' => "Surat keterangan kerja tidak ditemukan !"
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
                'message' => "Surat keterangan kerja tidak ditemukan !"
            ], 404);
        }

        $filename = $letter->id . '.docx';
        $fileNameServerDocx = "app/tmp/surat_keterangan_kerja/" . $filename;
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
                'message' => "Surat keterangan kerja tidak ditemukan !"
            ], 404);
        }

        if ($letter->signed_file != null) {
            $fileNameServerPdf = 'app/signed_files/' . $letter->signed_file;
            return response()->download(storage_path($fileNameServerPdf), $letter->signed_file);
        }

        $fileNameServerDocx = "app/tmp/surat_keterangan_kerja/" . $letter->id . '.docx';
        $templateProcessor = $letter->generate_docx();
        $templateProcessor->setValue('tanda_tangan', "");
        $templateProcessor->saveAs(storage_path($fileNameServerDocx));

        $response = Http::post(env('APP_DOCX_CONVERTER_URL') . '/convert', ['file_path' => $fileNameServerDocx]);
        if ($response->failed()) {
            return response()->json([
                'errors' => $response->json(),
            ], 500);
        }

        $filename = $letter->id . '.pdf';
        $tmpFileNameServerPdf = 'app/tmp/surat_keterangan_kerja/' . $filename;
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
                'message' => "Surat keterangan kerja tidak ditemukan !"
            ], 404);
        }
        if (!$letter->can_edit()) {
            return response()->json([
                'message' => "Surat keterangan kerja tidak dapat di edit !"
            ], 403);
        }

        $validate = Validator::make($request->all(), [
            'letter_template_id' => 'required|exists:letter_templates,id',
            'tanggal_surat' => 'required|date',
            'employee.id' => 'required|exists:employees,id',
            'employee.position' => 'required|string',
            'signer.id' => 'required|exists:employees,id',
            'signer.position' => 'required|string',
            'signature_type' => 'required|in:manual,digital,gambar tanda tangan',
        ]);
        if ($validate->fails()) {
            $response = [
                'errors' => $validate->errors(),
                'message' => "Validasi form gagal !"
            ];
            return response()->json($response, 422);
        }

        $letter->letter_template_id = $request->letter_template_id;
        $letter->tanggal_surat = $request->tanggal_surat;
        $letter->employee_id = $request->employee['id'];
        $letter->position = $request->employee['position'];
        $letter->signer_id = $request->signer['id'];
        $letter->signer_position = $request->signer['position'];
        $letter->signature_type = $request->signature_type;
        $letter->save();

        $response = [
            'message' => "Berhasil mengubah surat keterangan kerja !"
        ];
        return response()->json($response, 200);
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
                'message' => "Surat keterangan kerja tidak ditemukan !"
            ], 404);
        }

        if (!$letter->can_upload_verified_file()) {
            return response()->json([
                'message' => "Surat dengan jenis tanda tangan selain manual tidak dapat ditandatangani dengan upload file!"
            ], 403);
        }

        $file = $request->file('signed_file');
        $fileName = Str::replace("/", "-", $letter->get_reference_number()) . Str::random(4) . '.' . $file->getClientOriginalExtension();
        $fileNameServer = 'signed_files/' . $fileName;
        Storage::put($fileNameServer, file_get_contents($file));

        $letter->signed_file = $fileName;
        $letter->is_signed = true;
        $letter->save();

        $response = [
            'message' => "Berhasil mengupload file surat keterangan kerja !"
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
                'message' => "Surat keterangan kerja tidak ditemukan !"
            ], 404);
        }

        if (!$letter->can_edit()) {
            return response()->json([
                'message' => "Surat keterangan kerja tidak dapat dihapus !"
            ], 403);
        }
        $old_file = $letter->id . '.pdf';
        $letter->delete();

        $tmpFileNameServerPdf = 'app/tmp/surat_keterangan_kerja/' . $old_file;
        if (file_exists(storage_path($tmpFileNameServerPdf))) {
            unlink(storage_path($tmpFileNameServerPdf));
        }

        $response = [
            'message' => "Berhasil menghapus surat keterangan kerja !"
        ];
        return response()->json($response, 200);
    }

    public function give_reference_number(Request $request)
    {
        $letter = Letter::find($request->id);
        if (!$letter) {
            return response()->json([
                'message' => "Surat keterangan kerja tidak ditemukan !"
            ], 404);
        }

        //get the latest reference_number
        $latest_number = Letter::where('reference_number', '!=', 'NULL')->whereMonth('tanggal_surat', Carbon::now()->month)->whereYear('tanggal_surat', Carbon::now()->year)->count();
        $letter->reference_number = ReferenceNumberSetting::get_and_parse_reference_number_with_date(Letter::NAME, $latest_number + 1, $letter->tanggal_surat);
        $filename = $letter->id;
        $fileNameServerPdf = 'app/tmp/surat_keterangan_kerja/' . $filename . '.pdf';
        $fileNameServerDocx = 'app/tmp/surat_keterangan_kerja/' . $filename . '.docx';
        if (file_exists(storage_path($fileNameServerPdf))) {
            unlink(storage_path($fileNameServerPdf));
        }
        if (file_exists(storage_path($fileNameServerDocx))) {
            unlink(storage_path($fileNameServerDocx));
        }
        $letter->save();

        $response = [
            'message' => "Berhasil memberikan nomor surat !"
        ];

        return response()->json($response, 200);
    }

    public function sign(Request $request, $id)
    {
        $letter = Letter::find($id);

        if (!$letter) {
            return response()->json([
                'message' => "Surat keterangan kerja tidak ditemukan !"
            ], 404);
        }

        if ($letter->can_signed()) {
            return response()->json([
                'message' => "Surat keterangan kerja tidak dapat ditanda tangani !"
            ], 404);
        }

        if ($letter->signature_type == 'digital') {
            $validate = Validator::make($request->all(), [
                'password' => 'required|string',
            ]);

            if ($validate->fails()) {
                $response = [
                    'errors' => $validate->errors(),
                    'message' => "Wajib mengisi password untuk tanda tangan digital !"
                ];
                return response()->json($response, 422);
            }
        }

        if ($letter->signature_type == 'digital') {
            // $keypair = KeyPair::where('user_id', auth()->id())->first();
            // $data = $keypair->encrypt($request->password, $letter->id);
            // // var_dump(openssl_error_string());
            // return $data;
            // if (!$data) {
            //     return response()->json([
            //         'message' => "Gagal mengenkripsi data, password atau kunci pribadi tidak valid !"
            //     ], 422);
            // }
            // $qrcode = QrCode::size(300)->format('png')->generate($data);
            // $templateProcessor->setImageValue('tanda_tangan', $qrcode);
        } else {
            // $templateProcessor->setImageValue('tanda_tangan', storage_path('app/signature/' . $letter->signer->signature));
        }

        $letter->is_signed = true;
        $letter->save();
        return response()->json([
            'message' => "Berhasil menandatangani surat keterangan kerja !"
        ], 200);
    }
}
