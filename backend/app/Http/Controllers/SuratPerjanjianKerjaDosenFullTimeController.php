<?php

namespace App\Http\Controllers;

use App\Http\Resources\SuratPerjanjianKerjaDosenFullTimeCollection as ThisCollection;
use App\Http\Resources\SuratPerjanjianKerjaDosenFullTimeResource as ThisResource;
use App\Models\KeyPair;
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
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SuratPerjanjianKerjaDosenFullTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $letters = Letter::search($request->search)->whereUser(auth()->user())->paginate();
        return new ThisCollection($letters);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'letter_template_id' => 'required|exists:letter_templates,id',
            'nomor_surat_sebelumnya' => 'nullable|string',
            'employee.id' => 'required|exists:employees,id',
            'profesi' => 'required|string',
            'jabatan_fungsional' => 'required|string',
            'prodi' => 'required|exists:prodi,id',
            'mulai_berlaku' => 'required|date',
            'akhir_berlaku' => 'required|date|after:mulai_berlaku',
            'rekening' => 'required|exists:rekening,id',
            'signer.id' => 'required|exists:employees,id',
            'signer.position' => 'required|string',
            'signature_type' => 'required|in:manual,qrcode,digital,gambar tanda tangan',
            'pertelaan_perjanjian_kerja.pendidikan' => 'required|string',
            'pertelaan_perjanjian_kerja.jangka_waktu' => 'required|string',
            'pertelaan_perjanjian_kerja.tahun_satu' => 'required|date_format:Y',
            'pertelaan_perjanjian_kerja.tunjangan_dasar_satu' => 'required|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_fungsional_satu' => 'required|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_struktural_satu' => 'required|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_kemahalan_satu' => 'required|numeric',
            'pertelaan_perjanjian_kerja.pendapatan_bulanan_satu' => 'required|numeric',
            'pertelaan_perjanjian_kerja.tahun_dua' => 'required|date_format:Y',
            'pertelaan_perjanjian_kerja.tunjangan_dasar_dua' => 'required|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_fungsional_dua' => 'required|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_struktural_dua' => 'required|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_kemahalan_dua' => 'required|numeric',
            'pertelaan_perjanjian_kerja.pendapatan_bulanan_dua' => 'required|numeric',
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
            $letter->employee_id = $request->employee['id'];
            $letter->profesi = $request->profesi;
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
            $pertelaan->jangka_waktu = $request->pertelaan_perjanjian_kerja['jangka_waktu'];
            $pertelaan->tahun_satu = $request->pertelaan_perjanjian_kerja['tahun_satu'];
            $pertelaan->tunjangan_dasar_satu = $request->pertelaan_perjanjian_kerja['tunjangan_dasar_satu'];
            $pertelaan->tunjangan_fungsional_satu = $request->pertelaan_perjanjian_kerja['tunjangan_fungsional_satu'];
            $pertelaan->tunjangan_struktural_satu = $request->pertelaan_perjanjian_kerja['tunjangan_struktural_satu'];
            $pertelaan->tunjangan_kemahalan_satu = $request->pertelaan_perjanjian_kerja['tunjangan_kemahalan_satu'];
            $pertelaan->pendapatan_bulanan_satu = $request->pertelaan_perjanjian_kerja['pendapatan_bulanan_satu'];
            $pertelaan->tahun_dua = $request->pertelaan_perjanjian_kerja['tahun_dua'];
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

        if ($letter->signed_file_docx != null) {
            $fileNameServerDocx = 'app\\signed_files\\surat_perjanjian_kerja_dosen_full_time\\' . $letter->signed_file_docx;
            return response()->download(storage_path($fileNameServerDocx), $letter->signed_file_docx);
        }

        $filename = $letter->id . '.docx';
        $fileNameServerDocx = "app\\tmp\\surat_perjanjian_kerja_dosen_full_time\\" . $filename;

        if (file_exists(storage_path($fileNameServerDocx))) {
            return response()->download(storage_path($fileNameServerDocx), $filename);
        }

        $templateProcessor = $letter->generate_docx();
        $templateProcessor->setValue('tanda_tangan', '');
        $templateProcessor->saveAs(storage_path($fileNameServerDocx));
        return response()->download(storage_path($fileNameServerDocx), $filename);
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
            $fileNameServerPdf = 'app\\signed_files\\surat_perjanjian_kerja_dosen_full_time\\' . $letter->signed_file;
            return response()->download(storage_path($fileNameServerPdf), $letter->signed_file);
        }

        $filename = $letter->id . '.pdf';
        $fileNameServerPdf = 'app/signed_files/surat_perjanjian_kerja_dosen_full_time/' . $filename;

        if ($letter->signed_file_docx != null) {
            $response = Http::post(env('APP_DOCX_CONVERTER_URL') . '/convert', ['file_path' => storage_path($letter->signed_file_docx)]);
            if ($response->failed()) {
                return response()->json([
                    'errors' => "Something errors"
                ], 500);
            }

            if ($response->successful() && file_exists(storage_path($fileNameServerPdf))) {
                $letter->signed_file = $filename;
                $letter->save();
                return response()->download(storage_path($fileNameServerPdf), $filename);
            }
        }

        $tmpFileNameServerPdf = 'app/tmp/surat_perjanjian_kerja_dosen_full_time/' . $filename;
        if (file_exists(storage_path($tmpFileNameServerPdf))) {
            return response()->download(storage_path($tmpFileNameServerPdf), $filename);
        }

        $fileNameServerDocx = "app\\tmp\\surat_perjanjian_kerja_dosen_full_time\\" . $letter->id . '.docx';
        $templateProcessor = $letter->generate_docx();
        $templateProcessor->setValue('tanda_tangan', "");
        $templateProcessor->saveAs(storage_path($fileNameServerDocx));

        $response = Http::post(env('APP_DOCX_CONVERTER_URL') . '/convert', ['file_path' => storage_path($fileNameServerDocx)]);
        if ($response->failed()) {
            return response()->json([
                'errors' => "Something errors"
            ], 500);
        }

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
            'nomor_surat_sebelumnya' => 'nullable|string',
            'employee.id' => 'required|exists:employees,id',
            'profesi' => 'required|string',
            'jabatan_fungsional' => 'required|string',
            'prodi' => 'required|exists:prodi,id',
            'mulai_berlaku' => 'required|date',
            'akhir_berlaku' => 'required|date|after:mulai_berlaku',
            'rekening' => 'required|exists:rekening,id',
            'signer.id' => 'required|exists:employees,id',
            'signer.position' => 'required|string',
            'signature_type' => 'required|in:manual,qrcode,digital,gambar tanda tangan',
            'pertelaan_perjanjian_kerja.pendidikan' => 'required|string',
            'pertelaan_perjanjian_kerja.jangka_waktu' => 'required|string',
            'pertelaan_perjanjian_kerja.tahun_satu' => 'required|date_format:Y',
            'pertelaan_perjanjian_kerja.tunjangan_dasar_satu' => 'required|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_fungsional_satu' => 'required|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_struktural_satu' => 'required|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_kemahalan_satu' => 'required|numeric',
            'pertelaan_perjanjian_kerja.pendapatan_bulanan_satu' => 'required|numeric',
            'pertelaan_perjanjian_kerja.tahun_dua' => 'required|date_format:Y',
            'pertelaan_perjanjian_kerja.tunjangan_dasar_dua' => 'required|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_fungsional_dua' => 'required|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_struktural_dua' => 'required|numeric',
            'pertelaan_perjanjian_kerja.tunjangan_kemahalan_dua' => 'required|numeric',
            'pertelaan_perjanjian_kerja.pendapatan_bulanan_dua' => 'required|numeric',
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
            $letter->employee_id = $request->employee['id'];
            $letter->profesi = $request->profesi;
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
            $pertelaan->jangka_waktu = $request->pertelaan_perjanjian_kerja['jangka_waktu'];
            $pertelaan->tahun_satu = $request->pertelaan_perjanjian_kerja['tahun_satu'];
            $pertelaan->tunjangan_dasar_satu = $request->pertelaan_perjanjian_kerja['tunjangan_dasar_satu'];
            $pertelaan->tunjangan_fungsional_satu = $request->pertelaan_perjanjian_kerja['tunjangan_fungsional_satu'];
            $pertelaan->tunjangan_struktural_satu = $request->pertelaan_perjanjian_kerja['tunjangan_struktural_satu'];
            $pertelaan->tunjangan_kemahalan_satu = $request->pertelaan_perjanjian_kerja['tunjangan_kemahalan_satu'];
            $pertelaan->pendapatan_bulanan_satu = $request->pertelaan_perjanjian_kerja['pendapatan_bulanan_satu'];
            $pertelaan->tahun_dua = $request->pertelaan_perjanjian_kerja['tahun_dua'];
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
                'message' => "Surat dengan jenis tanda tangan selain manual dan digital tidak dapat ditandatangani dengan !"
            ], 403);
        }

        $file = $request->file('signed_file');
        $fileName = Str::replace("/", "-", $letter->get_reference_number()) . '.' . $file->getClientOriginalExtension();
        $fileNameServer = 'signed_files/' . $fileName;
        Storage::put($fileNameServer, file_get_contents($file));

        $letter->signed_file = $fileName;
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
        //
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
        $latest_number = Letter::where('reference_number', '!=', 'NULL')->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->count();
        $letter->reference_number = ReferenceNumberSetting::get_and_parse_reference_number_with_date(Letter::NAME, $latest_number + 1, $letter->created_at);
        $letter->save();

        $response = [
            'message' => "Berhasil memberikan nomor surat !"
        ];

        return response()->json($response, 200);
    }

    public function sign(Request $request, $id)
    {
        $letter = Letter::find($id);

        if (!$letter && $letter->can_sign()) {
            return response()->json([
                'message' => "Surat keterangan kerja tidak ditemukan !"
            ], 404);
        }

        if ($letter->signature_type == 'qrcode') {
            $validate = Validator::make($request->all(), [
                'password' => 'required|string',
            ]);

            if ($validate->fails()) {
                $response = [
                    'errors' => $validate->errors(),
                    'message' => "Wajib mengisi password untuk tanda tangan qrcode !"
                ];
                return response()->json($response, 422);
            }
        }

        $templateProcessor = $letter->generate_docx();
        if ($letter->signature_type == 'qrcode') {
            $keypair = KeyPair::where('user_id', auth()->id())->first();
            $data = $keypair->encrypt($request->password, $letter->id);
            // var_dump(openssl_error_string());
            return $data;
            if (!$data) {
                return response()->json([
                    'message' => "Gagal mengenkripsi data, password atau kunci pribadi tidak valid !"
                ], 422);
            }
            $qrcode = QrCode::size(300)->format('png')->generate($data);
            $templateProcessor->setImageValue('tanda_tangan', $qrcode);
        } else {
            $templateProcessor->setImageValue('tanda_tangan', storage_path('app/signature/' . $letter->signer->signature));
        }
        $filename = $letter->id;
        $fileNameServerDocx = "app\\signed_files\\surat_perjanjian_kerja_dosen_full_time\\" . $filename . '.docx';
        $templateProcessor->saveAs(storage_path($fileNameServerDocx));
        $letter->signed_file_docx = $fileNameServerDocx;
        $letter->save();
        return response()->json([
            'message' => "Berhasil menandatangani surat keterangan kerja !"
        ], 200);
    }
}
