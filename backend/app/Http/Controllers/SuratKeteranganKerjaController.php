<?php

namespace App\Http\Controllers;

use App\Http\Resources\SuratKeteranganKerjaCollection as ThisCollection;
use App\Models\ReferenceNumberSetting;
use App\Models\SuratKeteranganKerja as Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use NcJoes\OfficeConverter\OfficeConverter;

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'letter_template_id' => 'required|exists:letter_templates,id',
            'employee.nip' => 'required|exists:employees,nip',
            'employee.position' => 'required|string',
            'signer.nip' => 'required|exists:employees,nip',
            'signer.position' => 'required|string',
            'signature_type' => 'required|in:manual,qrcode,digital',
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
        $letter->employee_nip = $request->employee['nip'];
        $letter->position = $request->employee['position'];
        $letter->signer_nip = $request->signer['nip'];
        $letter->signer_position = $request->signer['position'];
        $letter->signature_type = $request->signature_type;
        $letter->created_by = auth()->user()->nip;
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
            'data' => $letter
        ], 200);
    }

    public function download(string $id)
    {
        $letter = Letter::find($id);
        if (!$letter) {
            return response()->json([
                'message' => "Surat keterangan kerja tidak ditemukan !"
            ], 404);
        }

        if ($letter->signed_file != null) {
            $fileNameServerPdf = 'app\\signed_files\\' . $letter->signed_file;
            return response()->download(storage_path($fileNameServerPdf), $letter->signed_file);
        }

        $filename = $letter->id . '.pdf';
        $fileNameServerPdf = 'app/tmp/surat_keterangan_kerja/' . $filename;

        if ($letter->tmp_file && file_exists(storage_path($letter->tmp_file))) {
            return response()->download(storage_path($letter->tmp_file), $filename);
        }

        $templateProcessor = $letter->generate_docx();
        $fileNameServerDocx = "app\\tmp\\surat_keterangan_kerja\\" . $letter->id . '.docx';
        $templateProcessor->saveAs(storage_path($fileNameServerDocx));

        $response = Http::post(env('APP_DOCX_CONVERTER_URL') . '/convert', ['file_path' => storage_path($fileNameServerDocx)]);
        if ($response->failed()) {
            return response()->json([
                'errors' => "Something errors"
            ], 500);
        }

        if ($response->successful() && file_exists(storage_path($fileNameServerPdf))) {
            unlink(storage_path($fileNameServerDocx));
            $letter->tmp_file = $fileNameServerPdf;
            $letter->save();
            return response()->download(storage_path($fileNameServerPdf), $filename);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
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
            'message' => "Berhasil mengupload file surat keterangan kerja !"
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
                'message' => "Surat keterangan kerja tidak ditemukan !"
            ], 404);
        }

        //get the latest reference_number
        $latest_number = Letter::where('reference_number', '!=', 'NULL')->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->count();
        $letter->reference_number = ReferenceNumberSetting::get_and_parse_reference_number_with_date(Letter::NAME, $latest_number + 1, $letter->created_at);
        $letter->tmp_file = null;
        $letter->save();

        $response = [
            'message' => "Berhasil memberikan nomor surat !"
        ];

        return response()->json($response, 200);
    }
}
