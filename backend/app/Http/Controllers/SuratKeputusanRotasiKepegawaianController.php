<?php

namespace App\Http\Controllers;

use App\Http\Resources\SuratKeputusanRotasiKepegawaianCollection as ThisCollection;
use App\Http\Resources\SuratKeputusanRotasiKepegawaianResource as ThisResource;
use App\Models\KeyPair;
use App\Models\ReferenceNumberSetting;
use App\Models\SuratKeputusanRotasiKepegawaian as Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SuratKeputusanRotasiKepegawaianController extends Controller
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
            'nomor_berita_acara' => "required|string",
            'tanggal_berita_acara' => "required|date",
            'employee.id' => 'required|exists:employees,id',
            'employee.status_awal' => 'required|string',
            'employee.jabatan_awal' => 'required|string',
            'employee.status_akhir' => 'required|string',
            'employee.jabatan_akhir' => 'required|string',
            'signer.id' => 'required|exists:employees,id',
            'signer.position' => 'required|string',
            'signature_type' => 'required|in:manual,qrcode,digital,gambar tanda tangan',
            'tanggal_berlaku' => 'required|date',
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
        $letter->nomor_berita_acara = $request->nomor_berita_acara;
        $letter->tanggal_berita_acara = $request->tanggal_berita_acara;
        $letter->employee_id = $request->employee['id'];
        $letter->status_awal = $request->employee['status_awal'];
        $letter->jabatan_awal = $request->employee['jabatan_awal'];
        $letter->status_akhir = $request->employee['status_akhir'];
        $letter->jabatan_akhir = $request->employee['jabatan_akhir'];
        $letter->signer_id = $request->signer['id'];
        $letter->signer_position = $request->signer['position'];
        $letter->signature_type = $request->signature_type;
        $letter->tanggal_berlaku = $request->tanggal_berlaku;
        $letter->created_by = auth()->user()->id;
        $letter->save();

        $response = [
            'message' => "Berhasil membuat Surat keputusan rotasi kepegawaian !"
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
                'message' => "Surat keputusan rotasi kepegawaian tidak ditemukan !"
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
                'message' => "Surat keputusan rotasi kepegawaian tidak ditemukan !"
            ], 404);
        }

        if ($letter->signed_file_docx != null) {
            $fileNameServerDocx = 'app\\signed_files\\surat_keputusan_rotasi_kepegawaian\\' . $letter->signed_file_docx;
            return response()->download(storage_path($fileNameServerDocx), $letter->signed_file_docx);
        }

        $filename = $letter->id . '.docx';
        $fileNameServerDocx = "app\\tmp\\surat_keputusan_rotasi_kepegawaian\\" . $filename;

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
                'message' => "Surat keputusan rotasi kepegawaian tidak ditemukan !"
            ], 404);
        }

        if ($letter->signed_file != null) {
            $fileNameServerPdf = 'app\\signed_files\\surat_keputusan_rotasi_kepegawaian\\' . $letter->signed_file;
            return response()->download(storage_path($fileNameServerPdf), $letter->signed_file);
        }

        $filename = $letter->id . '.pdf';
        $fileNameServerPdf = 'app/signed_files/surat_keputusan_rotasi_kepegawaian/' . $filename;

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

        $tmpFileNameServerPdf = 'app/tmp/surat_keputusan_rotasi_kepegawaian/' . $filename;
        if (file_exists(storage_path($tmpFileNameServerPdf))) {
            return response()->download(storage_path($tmpFileNameServerPdf), $filename);
        }

        $fileNameServerDocx = "app\\tmp\\surat_keputusan_rotasi_kepegawaian\\" . $letter->id . '.docx';
        $templateProcessor = $letter->generate_docx();
        $templateProcessor->setValue('tanda_tangan', "");
        $templateProcessor->saveAs(storage_path($fileNameServerDocx));

        $response = Http::post(env('APP_DOCX_CONVERTER_URL') . '/convert', ['file_path' => storage_path($fileNameServerDocx)]);
        if ($response->failed()) {
            return response()->json([
                'errors' => "Something errors"
            ], 500);
        }

        $tmpFileNameServerPdf = 'app/tmp/surat_keputusan_rotasi_kepegawaian/' . $filename;
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
                'message' => "Surat keputusan rotasi kepegawaian tidak ditemukan !"
            ], 404);
        }

        if (!$letter->can_edit()) {
            return response()->json([
                'message' => "Surat keputusan rotasi kepegawaian tidak dapat diubah !"
            ], 403);
        }

        $validate = Validator::make($request->all(), [
            'letter_template_id' => 'required|exists:letter_templates,id',
            'nomor_berita_acara' => "required|string",
            'tanggal_berita_acara' => "required|date",
            'employee.id' => 'required|exists:employees,id',
            'employee.status_awal' => 'required|string',
            'employee.jabatan_awal' => 'required|string',
            'employee.status_akhir' => 'required|string',
            'employee.jabatan_akhir' => 'required|string',
            'signer.id' => 'required|exists:employees,id',
            'signer.position' => 'required|string',
            'signature_type' => 'required|in:manual,qrcode,digital,gambar tanda tangan',
            'tanggal_berlaku' => 'required|date',
        ]);

        if ($validate->fails()) {
            $response = [
                'errors' => $validate->errors(),
                'message' => "Validasi form gagal !"
            ];
            return response()->json($response, 422);
        }

        $letter->letter_template_id = $request->letter_template_id;
        $letter->nomor_berita_acara = $request->nomor_berita_acara;
        $letter->tanggal_berita_acara = $request->tanggal_berita_acara;
        $letter->employee_id = $request->employee['id'];
        $letter->status_awal = $request->employee['status_awal'];
        $letter->jabatan_awal = $request->employee['jabatan_awal'];
        $letter->status_akhir = $request->employee['status_akhir'];
        $letter->jabatan_akhir = $request->employee['jabatan_akhir'];
        $letter->signer_id = $request->signer['id'];
        $letter->signer_position = $request->signer['position'];
        $letter->signature_type = $request->signature_type;
        $letter->tanggal_berlaku = $request->tanggal_berlaku;
        $letter->save();

        return response()->json([
            'message' => "Berhasil mengubah surat keputusan rotasi kepegawaian !"
        ], 200);
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
                'message' => "Surat keputusan rotasi kepegawaian tidak ditemukan !"
            ], 404);
        }

        if (!$letter->can_upload_verified_file()) {
            return response()->json([
                'message' => "Surat dengan jenis tanda tangan selain manual dan digital tidak dapat ditandatangani dengan upload file !"
            ], 403);
        }

        $file = $request->file('signed_file');
        $fileName = Str::replace("/", "-", $letter->get_reference_number()) . '.' . $file->getClientOriginalExtension();
        $fileNameServer = 'signed_files/' . $fileName;
        Storage::put($fileNameServer, file_get_contents($file));

        $letter->signed_file = $fileName;
        $letter->save();

        $response = [
            'message' => "Berhasil mengupload file surat keputusan rotasi kepegawaian !"
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
                'message' => "Surat keputusan rotasi kepegawaian tidak ditemukan !"
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
                'message' => "Surat keputusan rotasi kepegawaian tidak ditemukan !"
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
        $fileNameServerDocx = "app\\signed_files\\surat_keputusan_rotasi_kepegawaian\\" . $filename . '.docx';
        $templateProcessor->saveAs(storage_path($fileNameServerDocx));
        $letter->signed_file_docx = $fileNameServerDocx;
        $letter->save();
        return response()->json([
            'message' => "Berhasil menandatangani surat keputusan rotasi kepegawaian !"
        ], 200);
    }
}
