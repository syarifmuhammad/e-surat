<?php

namespace App\Http\Controllers;

use App\Http\Resources\SuratKeteranganKerjaCollection as ThisCollection;
use App\Models\ReferenceNumberSetting;
use App\Models\SuratKeteranganKerja as Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

    public function download(string $id) {
        $letter = Letter::find($id);
        if (!$letter) {
            return response()->json([
                'message' => "Surat keterangan kerja tidak ditemukan !"
            ], 404);
        }
        $templateProcessor = $letter->generate_docx();
        $fileNameServerDocx = 'app/tmp/' . Str::replace("/", "-", $letter->get_reference_number()) . '.docx';
        $templateProcessor->saveAs(storage_path($fileNameServerDocx));

        $domPdfPath = base_path('vendor/dompdf/dompdf');
        \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');
         
        //Load word file
        $Content = \PhpOffice\PhpWord\IOFactory::load(storage_path($fileNameServerDocx)); 
 
        //Save it into PDF
        $fileNameServerPdf = 'app/tmp/' . Str::replace("/", "-", $letter->get_reference_number()) . '.pdf';
        $PDFWriter = \PhpOffice\PhpWord\IOFactory::createWriter($Content,'PDF');
        $PDFWriter->save(storage_path($fileNameServerPdf));

        unlink(storage_path($fileNameServerDocx));

        $filename = Str::replace("/", "-", $letter->get_reference_number()) . '.pdf';
        return response()->download(storage_path($fileNameServerPdf), $filename)->deleteFileAfterSend(true);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function give_reference_number(Request $request) {
        $letter = Letter::find($request->id);
        if (!$letter) {
            return response()->json([
                'message' => "Surat keterangan kerja tidak ditemukan !"
            ], 404);
        }

        //get the latest reference_number
        $latest_number = Letter::where('reference_number', "IS NOT NULL")->count();
        $letter->reference_number = ReferenceNumberSetting::get_and_parse_reference_number_with_date(Letter::NAME, $latest_number + 1, $letter->created_at);
        $letter->save();

        $response = [
            'message' => "Berhasil memberikan nomor surat !"
        ];
        return response()->json($response, 200);
    }
}
