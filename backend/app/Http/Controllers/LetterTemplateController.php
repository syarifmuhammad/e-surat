<?php

namespace App\Http\Controllers;

use App\Http\Resources\LetterTemplateCollection;
use App\Models\LetterTemplate;
use App\Models\ReferenceNumberSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LetterTemplateController extends Controller
{
    public function index(Request $request)
    {
        if (!isset($request->letter_type)) {
            $letter_templates = LetterTemplate::paginate();
            return new LetterTemplateCollection($letter_templates);
        }

        $letter_types = collect(ReferenceNumberSetting::$DEFAULT)->pluck('letter_type')->toArray();
        $validate = Validator::make($request->all(), [
            'letter_type' => 'required|string|in:' . implode(',', $letter_types)
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => "",
                'errors' => $validate->errors()
            ], 422);
        }
        $letter_templates = LetterTemplate::where('letter_type', $request->letter_type)->get();

        return response()->json([
            'data' => $letter_templates,
        ]);
    }

    public function download($id)
    {
        $template = LetterTemplate::find($id);
        if (!$template) {
            return response()->json([
                'message' => "Template surat tidak ditemukan !"
            ], 404);
        }

        $filename = $template->file;
        $fileNameServer = 'app/letter_templates/' . $filename;

        if (file_exists(storage_path($fileNameServer))) {
            return response()->download(storage_path($fileNameServer), $template->id . '-' . $template->name . '-' . ReferenceNumberSetting::$DEFAULT[$template->letter_type]['name']);
        } else {
            return response()->json([
                'message' => "Template surat tidak ditemukan !"
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $template = LetterTemplate::find($id);
        if (!$template) {
            return response()->json([
                'message' => "Template surat tidak ditemukan !"
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'name' => 'required|string',
            'file' => 'required|file|mimes:doc,docx,pdf'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => "",
                'errors' => $validate->errors()
            ], 422);
        }

        $template->name = $request->name;
        $template->file = $request->file('file')->store('letter_templates');
        $template->save();

        return response()->json([
            'message' => "Template surat berhasil diubah !",
            'data' => $template
        ]);
    }
}
