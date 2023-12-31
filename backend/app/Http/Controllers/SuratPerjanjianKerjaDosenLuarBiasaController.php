<?php

namespace App\Http\Controllers;

use App\Http\Resources\SuratPerjanjianKerjaDosenLuarBiasaCollection as ThisCollection;
use App\Http\Resources\SuratPerjanjianKerjaDosenLuarBiasaResource as ThisResource;
use App\Models\KeyPair;
use App\Models\ReferenceNumberSetting;
use App\Models\Rekening;
use App\Models\SuratPerjanjianKerjaDosenLuarBiasa as Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SuratPerjanjianKerjaDosenLuarBiasaController extends Controller
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
            'tanggal_surat' => 'required|date',
            'employee.id' => 'required|exists:employees,id',
            'jabatan_fungsional' => 'required|string',
            'nidn' => 'required|string',
            'mata_kuliah' => 'required|string',
            'tahun_ajaran' => [
                'required',
                'regex:/^\d{4}\/\d{4}$/',
                function ($attribute, $value, $fail) {
                    // Pisahkan tahun sebelah kiri dan kanan
                    list($tahunKiri, $tahunKanan) = explode('/', $value);

                    // Periksa apakah tahun sebelah kiri kurang dari tahun sebelah kanan
                    if ($tahunKiri >= $tahunKanan) {
                        $fail($attribute . ' tidak valid. Tahun sebelah kiri harus kurang dari tahun sebelah kanan.');
                    }
                },
            ],
            'semester' => 'required|in:ganjil,genap',
            'rekening' => 'required|exists:rekening,id',
            'upah' => 'required|numeric',
            'transportasi' => 'required|numeric',
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
        $letter->jabatan_fungsional = $request->jabatan_fungsional;
        $letter->nidn = $request->nidn;
        $letter->mata_kuliah = $request->mata_kuliah;
        $letter->tahun_ajaran = $request->tahun_ajaran;
        $letter->semester = $request->semester;
        $rekening = Rekening::find($request->rekening);
        $letter->rekening = json_encode($rekening);
        $letter->upah = $request->upah;
        $letter->transportasi = $request->transportasi;
        $letter->signer_id = $request->signer['id'];
        $letter->signer_position = $request->signer['position'];
        $letter->signature_type = $request->signature_type;
        $letter->created_by = auth()->user()->id;
        $letter->save();

        $response = [
            'message' => "Berhasil membuat Surat perjanjian kerja dosen luar biasa !"
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
                'message' => "Surat perjanjian kerja dosen luar biasa tidak ditemukan !"
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
                'message' => "Surat perjanjian kerja dosen luar biasa tidak ditemukan !"
            ], 404);
        }

        $filename = $letter->id . '.docx';
        $fileNameServerDocx = "app/tmp/surat_perjanjian_kerja_dosen_luar_biasa/" . $filename;
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
                'message' => "Surat perjanjian kerja dosen luar biasa tidak ditemukan !"
            ], 404);
        }

        if ($letter->signed_file != null) {
            $fileNameServerPdf = 'app/signed_files/surat_perjanjian_kerja_dosen_luar_biasa/' . $letter->signed_file;
            return response()->download(storage_path($fileNameServerPdf), $letter->signed_file);
        }

        $fileNameServerDocx = "app/tmp/surat_perjanjian_kerja_dosen_luar_biasa/" . $letter->id . '.docx';
        $templateProcessor = $letter->generate_docx();
        $templateProcessor->setValue('tanda_tangan', "");
        $templateProcessor->saveAs(storage_path($fileNameServerDocx));

        $response = Http::post(env('APP_DOCX_CONVERTER_URL') . '/convert', ['file_path' => storage_path($fileNameServerDocx)]);
        if ($response->failed()) {
            return response()->json([
                'errors' => "Something errors"
            ], 500);
        }
        
        $filename = $letter->id . '.pdf';
        $tmpFileNameServerPdf = 'app/tmp/surat_perjanjian_kerja_dosen_luar_biasa/' . $filename;
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
                'message' => "Surat perjanjian kerja dosen luar biasa tidak ditemukan !"
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'letter_template_id' => 'required|exists:letter_templates,id',
            'tanggal_surat' => 'required|date',
            'employee.id' => 'required|exists:employees,id',
            'jabatan_fungsional' => 'required|string',
            'nidn' => 'required|string',
            'mata_kuliah' => 'required|string',
            'tahun_ajaran' => [
                'required',
                'regex:/^\d{4}\/\d{4}$/',
                function ($attribute, $value, $fail) {
                    // Pisahkan tahun sebelah kiri dan kanan
                    list($tahunKiri, $tahunKanan) = explode('/', $value);

                    // Periksa apakah tahun sebelah kiri kurang dari tahun sebelah kanan
                    if ($tahunKiri >= $tahunKanan) {
                        $fail($attribute . ' tidak valid. Tahun sebelah kiri harus kurang dari tahun sebelah kanan.');
                    }
                },
            ],
            'semester' => 'required|in:ganjil,genap',
            'rekening' => 'required|exists:rekening,id',
            'upah' => 'required|numeric',
            'transportasi' => 'required|numeric',
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
        $letter->jabatan_fungsional = $request->jabatan_fungsional;
        $letter->nidn = $request->nidn;
        $letter->mata_kuliah = $request->mata_kuliah;
        $letter->tahun_ajaran = $request->tahun_ajaran;
        $letter->semester = $request->semester;
        $rekening = Rekening::find($request->rekening);
        $letter->rekening = json_encode($rekening);
        $letter->upah = $request->upah;
        $letter->transportasi = $request->transportasi;
        $letter->signer_id = $request->signer['id'];
        $letter->signer_position = $request->signer['position'];
        $letter->signature_type = $request->signature_type;
        $letter->save();

        $response = [
            'message' => "Berhasil mengubah surat perjanjian kerja dosen luar biasa !"
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
                'message' => "Surat perjanjian kerja dosen luar biasa tidak ditemukan !"
            ], 404);
        }

        if (!$letter->can_upload_verified_file()) {
            return response()->json([
                'message' => "Surat dengan jenis tanda tangan selain manual tidak dapat ditandatangani dengan !"
            ], 403);
        }

        $file = $request->file('signed_file');
        $fileName = Str::replace("/", "-", $letter->get_reference_number()) . '.' . $file->getClientOriginalExtension();
        $fileNameServer = 'signed_files/' . $fileName;
        Storage::put($fileNameServer, file_get_contents($file));

        $letter->signed_file = $fileName;
        $letter->is_signed = true;
        $letter->save();

        $response = [
            'message' => "Berhasil mengupload file Surat perjanjian kerja dosen luar biasa !"
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
                'message' => "Surat perjanjian kerja dosen luar biasa tidak ditemukan !"
            ], 404);
        }

        if (!$letter->can_edit()) {
            return response()->json([
                'message' => "Surat perjanjian kerja dosen luar biasa tidak dapat dihapus !"
            ], 403);
        }

        $old_file = $letter->id . '.pdf';
        $letter->delete();

        $tmpFileNameServerPdf = 'app/tmp/surat_perjanjian_kerja_dosen_luar_biasa/' . $old_file;
        if (file_exists(storage_path($tmpFileNameServerPdf))) {
            unlink(storage_path($tmpFileNameServerPdf));
        }

        return response()->json([
            'message' => "Berhasil menghapus surat perjanjian kerja dosen luar biasa !"
        ], 200);
    }

    public function give_reference_number(Request $request)
    {
        $letter = Letter::find($request->id);
        if (!$letter) {
            return response()->json([
                'message' => "Surat perjanjian kerja dosen luar biasa tidak ditemukan !"
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
                'message' => "Surat perjanjian kerja dosen luar biasa tidak ditemukan !"
            ], 404);
        }

        if (!$letter->can_signed()) {
            return response()->json([
                'message' => "Surat perjanjian kerja dosen luar biasa tidak dapat ditandatangani !"
            ], 403);
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

        // $templateProcessor = $letter->generate_docx();
        // if ($letter->signature_type == 'digital') {
        //     $keypair = KeyPair::where('user_id', auth()->id())->first();
        //     $data = $keypair->encrypt($request->password, $letter->id);
        //     // var_dump(openssl_error_string());
        //     return $data;
        //     if (!$data) {
        //         return response()->json([
        //             'message' => "Gagal mengenkripsi data, password atau kunci pribadi tidak valid !"
        //         ], 422);
        //     }
        //     $qrcode = QrCode::size(300)->format('png')->generate($data);
        //     $templateProcessor->setImageValue('tanda_tangan', $qrcode);
        // } else {
        //     $templateProcessor->setImageValue('tanda_tangan', storage_path('app/signature/' . $letter->signer->signature));
        // }
        // $filename = $letter->id;
        // $fileNameServerDocx = "app/signed_files/surat_perjanjian_kerja_dosen_luar_biasa/" . $filename . '.docx';
        // $templateProcessor->saveAs(storage_path($fileNameServerDocx));
        // $letter->signed_file_docx = $fileNameServerDocx;
        $letter->is_signed = true;
        $letter->save();
        return response()->json([
            'message' => "Berhasil menandatangani surat perjanjian kerja dosen luar biasa !"
        ], 200);
    }
}
