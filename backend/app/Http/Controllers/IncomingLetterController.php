<?php

namespace App\Http\Controllers;

use App\Http\Resources\IncomingLetterCollection;
use App\Models\IncomingLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class IncomingLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $incoming_letters = IncomingLetter::search($request->search)->paginate();

        return new IncomingLetterCollection($incoming_letters);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|exists:category_incoming_letters,id',
            'reference_number' => 'required',
            'perihal' => 'required',
            'asal_surat' => 'required',
            'tanggal_surat' => 'required|date',
            'keterangan' => 'required',
            'file_surat' => 'required|file|mimes:pdf|max:10000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $incoming_letter = new IncomingLetter();
            $incoming_letter->category_id = $request->category;
            $incoming_letter->reference_number = $request->reference_number;
            $incoming_letter->perihal = $request->perihal;
            $incoming_letter->asal_surat = $request->asal_surat;
            $incoming_letter->tanggal_surat = $request->tanggal_surat;
            $incoming_letter->keterangan = $request->keterangan;
            $file = $request->file('file_surat')->store('incoming_letters');
            $file_surat = 'app/' . $file;
            $incoming_letter->file_surat = $file;
            $incoming_letter->created_by = auth()->user()->id;
            // $response = Http::post(env('APP_DOCX_CONVERTER_URL') . '/ocr', ['file_path' => storage_path($incoming_letter->file_surat)]);
            // if ($response->failed()) {
            //     throw new \Exception("Something errors");
            // }

            // if ($response->successful()) {
            //     $incoming_letter->ocr_text = $response->json()['data'];
            // }
            $incoming_letter->save();

            return response()->json([
                'message' => 'Berhasil menambahkan surat masuk.',
                'data' => $incoming_letter,
            ], 201);
        } catch (\Exception $e) {
            if (file_exists(storage_path($file_surat))) {
                unlink(storage_path($file_surat));
            }
            return response()->json([
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function file(string $id)
    {
        $incoming_letter = IncomingLetter::findOrFail($id);
        return response()->download(storage_path('app/' . $incoming_letter->file_surat));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|exists:category_incoming_letters,id',
            'reference_number' => 'required',
            'perihal' => 'required',
            'asal_surat' => 'required',
            'tanggal_surat' => 'required|date',
            'keterangan' => 'required',
            'file_surat' => 'required|file|mimes:pdf|max:10000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $incoming_letter = IncomingLetter::find($id);
            $incoming_letter->category_id = $request->category;
            $incoming_letter->reference_number = $request->reference_number;
            $incoming_letter->perihal = $request->perihal;
            $incoming_letter->asal_surat = $request->asal_surat;
            $incoming_letter->tanggal_surat = $request->tanggal_surat;
            $incoming_letter->keterangan = $request->keterangan;
            $file = $request->file('file_surat')->store('incoming_letters');
            $file_surat = 'app/' . $file;
            $old_file = $incoming_letter->file_surat;
            $incoming_letter->file_surat = $file;
            $incoming_letter->created_by = auth()->user()->id;
            // $response = Http::post(env('APP_DOCX_CONVERTER_URL') . '/ocr', ['file_path' => storage_path($incoming_letter->file_surat)]);
            // if ($response->failed()) {
            //     throw new \Exception("Something errors");
            // }

            // if ($response->successful()) {
            //     $incoming_letter->ocr_text = $response->json()['data'];
            // }
            $incoming_letter->save();

            if (file_exists(storage_path('app/' . $old_file))) {
                unlink(storage_path('app/' . $old_file));
            }

            return response()->json([
                'message' => 'Berhasil menambahkan surat masuk.',
                'data' => $incoming_letter,
            ], 201);
        } catch (\Exception $e) {
            if (file_exists(storage_path($file_surat))) {
                unlink(storage_path($file_surat));
            }
            return response()->json([
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $incoming_letter = IncomingLetter::find($id);

            if (!$incoming_letter) {
                return response()->json([
                    'message' => 'Surat masuk tidak ditemukan.',
                ], 404);
            }

            $old_file = $incoming_letter->file_surat;
            $incoming_letter->delete();

            if (file_exists(storage_path('app/' . $old_file))) {
                unlink(storage_path('app/' . $old_file));
            }

            return response()->json([
                'message' => 'Berhasil menghapus surat masuk.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}
