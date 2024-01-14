<?php

namespace App\Http\Controllers;

use App\Http\Resources\SuratPerjanjianKerjaDosenFullTimeCollection as ThisCollection;
use App\Http\Resources\SuratPerjanjianKerjaDosenFullTimeResource as ThisResource;
use App\Models\PertelaanPerjanjianKerja;
use App\Models\Prodi;
use App\Models\ReferenceNumberSetting;
use App\Models\Rekening;
use App\Models\SuratPerjanjianKerjaDosenFullTime as Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Crypt;

class SuratPerjanjianKerjaDosenFullTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $letters = Letter::search($request->search)->whereUser(auth()->user())->orderBy('is_signed')->orderBy('reference_number')->orderBy('id')->paginate();
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
            'nomor_surat_sebelumnya' => 'nullable|string',
            'tanggal_surat_sebelumnya' => 'nullable|date',
            'employee.id' => 'required|exists:employees,id',
            'jabatan_fungsional' => 'required|string',
            'prodi' => 'required|exists:prodi,id',
            'mulai_berlaku' => 'required|date',
            'akhir_berlaku' => 'required|date|after:mulai_berlaku',
            'rekening' => 'required|exists:rekening,id',
            'signer.id' => 'required|exists:employees,id',
            'signer.position' => 'required|string',
            'signature_type' => 'required|in:manual,digital,gambar tanda tangan',
            'pertelaan_perjanjian_kerja.pendidikan' => 'required|string',
            'pertelaan_perjanjian_kerja.tunjangan_dasar_satu' => 'present|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_fungsional_satu' => 'present|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_struktural_satu' => 'present|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_kemahalan_satu' => 'present|numeric',
            'pertelaan_perjanjian_kerja.pendapatan_bulanan_satu' => 'present|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_dasar_dua' => 'present|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_fungsional_dua' => 'present|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_struktural_dua' => 'present|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_kemahalan_dua' => 'present|numeric',
            'pertelaan_perjanjian_kerja.pendapatan_bulanan_dua' => 'present|numeric',
            'pertelaan_perjanjian_kerja.fasilitas_lainnya' => 'required|array',
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
            $letter->nomor_surat_sebelumnya = $request->nomor_surat_sebelumnya;
            $letter->tanggal_surat_sebelumnya = $request->tanggal_surat_sebelumnya;
            $letter->employee_id = $request->employee['id'];
            $letter->jabatan_fungsional = $request->jabatan_fungsional;

            $prodi = Prodi::find($request->prodi);
            $letter->prodi = json_encode($prodi);

            $letter->mulai_berlaku = $request->mulai_berlaku;
            $letter->akhir_berlaku = $request->akhir_berlaku;

            $rekening = Rekening::find($request->rekening);
            $letter->rekening = json_encode($rekening);

            $letter->signer_id = $request->signer['id'];
            $letter->signer_position = $request->signer['position'];
            $letter->signature_type = $request->signature_type;
            $letter->created_by = auth()->user()->id;
            $letter->save();

            $pertelaan = new PertelaanPerjanjianKerja();
            $pertelaan->id = $letter->id;
            $pertelaan->pendidikan = $request->pertelaan_perjanjian_kerja['pendidikan'];
            $pertelaan->tahun_satu = $request->pertelaan_perjanjian_kerja['tahun_satu'] ?? "";
            $pertelaan->tunjangan_dasar_satu = $request->pertelaan_perjanjian_kerja['tunjangan_dasar_satu'];
            $pertelaan->tunjangan_fungsional_satu = $request->pertelaan_perjanjian_kerja['tunjangan_fungsional_satu'];
            $pertelaan->tunjangan_struktural_satu = $request->pertelaan_perjanjian_kerja['tunjangan_struktural_satu'];
            $pertelaan->tunjangan_kemahalan_satu = $request->pertelaan_perjanjian_kerja['tunjangan_kemahalan_satu'];
            $pertelaan->pendapatan_bulanan_satu = $request->pertelaan_perjanjian_kerja['pendapatan_bulanan_satu'];
            $pertelaan->tahun_dua = $request->pertelaan_perjanjian_kerja['tahun_dua'] ?? "";
            $pertelaan->tunjangan_dasar_dua = $request->pertelaan_perjanjian_kerja['tunjangan_dasar_dua'];
            $pertelaan->tunjangan_fungsional_dua = $request->pertelaan_perjanjian_kerja['tunjangan_fungsional_dua'];
            $pertelaan->tunjangan_struktural_dua = $request->pertelaan_perjanjian_kerja['tunjangan_struktural_dua'];
            $pertelaan->tunjangan_kemahalan_dua = $request->pertelaan_perjanjian_kerja['tunjangan_kemahalan_dua'];
            $pertelaan->pendapatan_bulanan_dua = $request->pertelaan_perjanjian_kerja['pendapatan_bulanan_dua'];
            $pertelaan->fasilitas_lainnya = json_encode($request->pertelaan_perjanjian_kerja['fasilitas_lainnya']);
            $pertelaan->save();

            DB::commit();

            $response = [
                'message' => "Berhasil membuat Surat perjanjian kerja dosen full time !"
            ];
            return response()->json($response, 201);
        } catch (\Exception $e) {
            DB::rollback();
            $response = [
                'errors' => $e->getMessage(),
                'message' => "Terjadi kesalahan saat membuat Surat perjanjian kerja dosen full time !"
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
                'message' => "Surat perjanjian kerja dosen full time tidak ditemukan !"
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
                'message' => "Surat perjanjian kerja dosen full time tidak ditemukan !"
            ], 404);
        }

        $filename = $letter->id . '.docx';
        $fileNameServerDocx = "app/tmp/surat_perjanjian_kerja_dosen_full_time/" . $filename;
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
                'message' => "Surat perjanjian kerja dosen full time tidak ditemukan !"
            ], 404);
        }

        if ($letter->signed_file != null) {
            $fileNameServerPdf = 'app/signed_files/surat_perjanjian_kerja_dosen_full_time/' . $letter->signed_file;
            return response()->download(storage_path($fileNameServerPdf), $letter->signed_file);
        }

        $fileNameServerDocx = "app/tmp/surat_perjanjian_kerja_dosen_full_time/" . $letter->id . '.docx';
        $templateProcessor = $letter->generate_docx();
        $templateProcessor->setValue('tanda_tangan', "");
        $templateProcessor->saveAs(storage_path($fileNameServerDocx));

        $response = Http::post(env('APP_DOCX_CONVERTER_URL') . '/convert', ['file_path' => $fileNameServerDocx]);
        if ($response->failed()) {
            return response()->json([
                'errors' => "Something errors"
            ], 500);
        }
        
        $filename = $letter->id . '.pdf';
        $tmpFileNameServerPdf = 'app/tmp/surat_perjanjian_kerja_dosen_full_time/' . $filename;
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
                'message' => "Surat perjanjian kerja dosen full time tidak ditemukan !"
            ], 404);
        }

        if (!$letter->can_edit()) {
            return response()->json([
                'message' => "Surat perjanjian kerja dosen full time tidak dapat diedit !"
            ], 403);
        }

        $validate = Validator::make($request->all(), [
            'letter_template_id' => 'required|exists:letter_templates,id',
            'tanggal_surat' => 'required|date',
            'nomor_surat_sebelumnya' => 'nullable|string',
            'tanggal_surat_sebelumnya' => 'nullable|date',
            'employee.id' => 'required|exists:employees,id',
            'jabatan_fungsional' => 'required|string',
            'prodi' => 'required|exists:prodi,id',
            'mulai_berlaku' => 'required|date',
            'akhir_berlaku' => 'required|date|after:mulai_berlaku',
            'rekening' => 'required|exists:rekening,id',
            'signer.id' => 'required|exists:employees,id',
            'signer.position' => 'required|string',
            'signature_type' => 'required|in:manual,digital,gambar tanda tangan',
            'pertelaan_perjanjian_kerja.pendidikan' => 'required|string',
            'pertelaan_perjanjian_kerja.tunjangan_dasar_satu' => 'present|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_fungsional_satu' => 'present|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_struktural_satu' => 'present|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_kemahalan_satu' => 'present|numeric',
            'pertelaan_perjanjian_kerja.pendapatan_bulanan_satu' => 'present|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_dasar_dua' => 'present|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_fungsional_dua' => 'present|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_struktural_dua' => 'present|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_kemahalan_dua' => 'present|numeric',
            'pertelaan_perjanjian_kerja.pendapatan_bulanan_dua' => 'present|numeric',
            'pertelaan_perjanjian_kerja.fasilitas_lainnya' => 'required|array',
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
            $letter->nomor_surat_sebelumnya = $request->nomor_surat_sebelumnya;
            $letter->tanggal_surat_sebelumnya = $request->tanggal_surat_sebelumnya;
            $letter->employee_id = $request->employee['id'];
            $letter->jabatan_fungsional = $request->jabatan_fungsional;

            $prodi = Prodi::find($request->prodi);
            $letter->prodi = json_encode($prodi);

            $letter->mulai_berlaku = $request->mulai_berlaku;
            $letter->akhir_berlaku = $request->akhir_berlaku;

            $rekening = Rekening::find($request->rekening);
            $letter->rekening = json_encode($rekening);
            $letter->signer_id = $request->signer['id'];
            $letter->signer_position = $request->signer['position'];
            $letter->signature_type = $request->signature_type;
            $letter->created_by = auth()->user()->id;
            $letter->save();

            $pertelaan = PertelaanPerjanjianKerja::find($letter->id);
            $pertelaan->pendidikan = $request->pertelaan_perjanjian_kerja['pendidikan'];
            $pertelaan->tahun_satu = $request->pertelaan_perjanjian_kerja['tahun_satu'] ?? "";
            $pertelaan->tunjangan_dasar_satu = $request->pertelaan_perjanjian_kerja['tunjangan_dasar_satu'];
            $pertelaan->tunjangan_fungsional_satu = $request->pertelaan_perjanjian_kerja['tunjangan_fungsional_satu'];
            $pertelaan->tunjangan_struktural_satu = $request->pertelaan_perjanjian_kerja['tunjangan_struktural_satu'];
            $pertelaan->tunjangan_kemahalan_satu = $request->pertelaan_perjanjian_kerja['tunjangan_kemahalan_satu'];
            $pertelaan->pendapatan_bulanan_satu = $request->pertelaan_perjanjian_kerja['pendapatan_bulanan_satu'];
            $pertelaan->tahun_dua = $request->pertelaan_perjanjian_kerja['tahun_dua'] ?? "";
            $pertelaan->tunjangan_dasar_dua = $request->pertelaan_perjanjian_kerja['tunjangan_dasar_dua'];
            $pertelaan->tunjangan_fungsional_dua = $request->pertelaan_perjanjian_kerja['tunjangan_fungsional_dua'];
            $pertelaan->tunjangan_struktural_dua = $request->pertelaan_perjanjian_kerja['tunjangan_struktural_dua'];
            $pertelaan->tunjangan_kemahalan_dua = $request->pertelaan_perjanjian_kerja['tunjangan_kemahalan_dua'];
            $pertelaan->pendapatan_bulanan_dua = $request->pertelaan_perjanjian_kerja['pendapatan_bulanan_dua'];
            $pertelaan->fasilitas_lainnya = json_encode($request->pertelaan_perjanjian_kerja['fasilitas_lainnya']);
            $pertelaan->save();

            DB::commit();

            $response = [
                'message' => "Berhasil mengubah surat perjanjian kerja dosen full time !"
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            DB::rollback();
            $response = [
                'errors' => $e->getMessage(),
                'message' => "Terjadi kesalahan saat mengubah surat perjanjian kerja dosen full time !"
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
                'message' => "Surat perjanjian kerja dosen full time tidak ditemukan !"
            ], 404);
        }

        if (!$letter->can_upload_verified_file()) {
            return response()->json([
                'message' => "Surat dengan jenis tanda tangan selain manual tidak dapat ditandatangani dengan upload file !"
            ], 403);
        }

        $file = $request->file('signed_file');
        $fileName = "surat_perjanjian_kerjan_dosen_full_time_" . Str::replace("/", "-", $letter->get_reference_number()) . '.' . $file->getClientOriginalExtension();
        $fileNameServer = 'signed_files/' . $fileName;
        Storage::put($fileNameServer, file_get_contents($file));

        $letter->signed_file = $fileName;
        $letter->is_signed = true;
        $letter->save();

        $response = [
            'message' => "Berhasil mengupload file surat perjanjian kerja dosen full time !"
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
                'message' => "Surat perjanjian kerja dosen full time tidak ditemukan !"
            ], 404);
        }

        if (!$letter->can_edit()) {
            return response()->json([
                'message' => "Surat perjanjian kerja dosen full time tidak dapat dihapus !"
            ], 403);
        }

        $old_file = $letter->id . '.pdf';
        $letter->delete();

        $tmpFileNameServerPdf = 'app/tmp/surat_perjanjian_kerja_dosen_full_time/' . $old_file;
        if (file_exists(storage_path($tmpFileNameServerPdf))) {
            unlink(storage_path($tmpFileNameServerPdf));
        }

        return response()->json([
            'message' => "Berhasil menghapus surat perjanjian kerja dosen full time !"
        ], 200);
    }

    public function give_reference_number(Request $request)
    {
        $letter = Letter::find($request->id);
        if (!$letter) {
            return response()->json([
                'message' => "Surat perjanjian kerja dosen full time tidak ditemukan !"
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

    public function sign(Request $request, $id)
    {
        $letter = Letter::find($id);

        if (!$letter) {
            return response()->json([
                'message' => "Surat perjanjian kerja dosen full time tidak ditemukan !"
            ], 404);
        }

        if (!$letter->can_signed()) {
            return response()->json([
                'message' => "Surat perjanjian kerja dosen full time tidak dapat ditanda tangani !"
            ], 404);
        }
        $fileName = null;
        try {
            if ($letter->signature_type == 'digital') {
                $letter_as_base64 = base64_encode($letter);
                $encrypt_letter = Crypt::encrypt($letter_as_base64);
                $url_confirmation = env('APP_URL') . "/api/confirm-signature?data=$encrypt_letter";
                $fileName = "surat_perjanjian_kerja_dosen_full_time_" . Str::replace("/", "-", $letter->get_reference_number()) . '.png';
                QrCode::size(500)->generate($url_confirmation, storage_path('app/signed_files/' . $fileName));
            } else if ($letter->signature_type == 'gambar tanda tangan') {
                $signature = Employee::find(auth()->id())->signature;

                if (!$signature && !Storage::exists('signature/' . $signature)) {
                    return response()->json([
                        'message' => "Tanda tangan anda tidak ditemukan !"
                    ], 404);
                }

                $fileName = "surat_perjanjian_kerja_dosen_full_time_" . Str::replace("/", "-", $letter->get_reference_number()) . '.' . pathinfo(storage_path('signature/' . $signature), PATHINFO_EXTENSION);
                if (!Storage::copy('signature/' . $signature, 'signed_files/' . $fileName)) {
                    throw new \Exception("Gagal mengcopy gambar tanda tangan !");
                }
            }

            $letter->signed_file = $fileName;
            $letter->is_signed = true;
            $letter->save();
            return response()->json([
                'message' => "Berhasil menandatangani surat perjanjian kerja dosen full time !"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
                'message' => "Gagal menandatangani surat perjanjian kerja dosen full time !"
            ], 500);
        }
    }
}
