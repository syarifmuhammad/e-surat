<?php

namespace App\Http\Controllers;

use App\Http\Resources\LetterTemplateCollection;
use App\Models\LetterTemplate;
use App\Models\ReferenceNumberSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LetterTemplateController extends Controller
{
    public function index(Request $request)
    {
        if (!isset($request->letter_type)) {
            $letter_templates = LetterTemplate::search($request->search);
            if (isset($request->is_active)) {
                $letter_templates = $letter_templates->isActive($request->is_active);
            }
            $letter_templates = $letter_templates->paginate();
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

    public function show($id)
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
            return response()->json([
                'id' => $template->id,
                'name' => $template->name,
                'letter_type' => $template->letter_type,
                'file' => base64_encode(file_get_contents(storage_path($fileNameServer)))
            ], 200);
        } else {
            return response()->json([
                'message' => "Template surat tidak ditemukan !"
            ], 404);
        }
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
    
    public function download_example(Request $request)
    {
        $letter_types = collect(ReferenceNumberSetting::$DEFAULT)->pluck('letter_type')->toArray();
        $validator = Validator::make($request->all(), [
            'letter_type' => 'required|string|in:' . implode(',', $letter_types),
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'message' => "Kategori surat tidak ditemukan !"
            ];
            return response()->json($response, 422);
        }

        $filename = $request->letter_type . '.docx';
        $fileNameServer = 'app/default_templates/' . $filename;

        if (file_exists(storage_path($fileNameServer))) {
            return response()->download(storage_path($fileNameServer), $filename);
        } else {
            return response()->json([
                'message' => "Template surat tidak ditemukan !"
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $letter_types = collect(ReferenceNumberSetting::$DEFAULT)->pluck('letter_type')->toArray();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'letter_type' => 'required|string|in:' . implode(',', $letter_types),
            'file' => 'required|file|mimes:doc,docx,pdf'
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'message' => "Validasi form gagal !"
            ];
            return response()->json($response, 422);
        }

        DB::beginTransaction();
        try {
            $template = new LetterTemplate();
            $template->name = $request->name;
            $template->letter_type = $request->letter_type;
            if ($request->hasFile('file')) {
                $new_file = $request->file('file')->store('letter_templates');
                $new_file = str_replace('letter_templates/', '', $new_file);
                $template->file = $new_file;
            }
            $template->save();

            DB::commit();
            return response()->json([
                'message' => "Template surat berhasil ditambah !",
                'data' => $template
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->hasFile('file')) {
                unlink(storage_path('app/letter_templates/' . $new_file));
            }
            return response()->json([
                'message' => "Terjadi kesalahan saat mengubah template surat !",
                'errors' => $e->getMessage()
            ], 500);
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

        $letter_types = collect(ReferenceNumberSetting::$DEFAULT)->pluck('letter_type')->toArray();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'letter_type' => 'required|string|in:' . implode(',', $letter_types),
            'file' => 'file|mimes:doc,docx,pdf'
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'message' => "Validasi form gagal !"
            ];
            return response()->json($response, 422);
        }

        $count_template = LetterTemplate::where('letter_type', $template->letter_type)->count();
        if ($count_template == 1 && $template->letter_type != $request->letter_type) {
            return response()->json([
                'errors' => [
                    'letter_type' => [
                        "Tidak dapat mengubah template surat karena hanya tersisa satu !"
                    ]
                ],
                'message' => "Tidak dapat mengubah template surat karena hanya tersisa satu !"
            ], 422);
        }
        DB::beginTransaction();
        try {
            $template->name = $request->name;
            $template->letter_type = $request->letter_type;
            if ($request->hasFile('file')) {
                $new_file = $request->file('file')->store('letter_templates');
                $new_file = str_replace('letter_templates/', '', $new_file);
                $old_file = $template->file;
                $template->file = $new_file;
            }
            $template->save();

            if ($request->hasFile('file')) {
                unlink(storage_path('app/letter_templates/' . $old_file));
            }

            DB::commit();
            return response()->json([
                'message' => "Template surat berhasil diubah !",
                'data' => $template
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->hasFile('file')) {
                unlink(storage_path('app/letter_templates/' . $new_file));
            }
            return response()->json([
                'message' => "Terjadi kesalahan saat mengubah template surat !",
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function set_active_or_not(Request $request, $id) {
        $template = LetterTemplate::find($id);
        if (!$template) {
            return response()->json([
                'message' => "Template surat tidak ditemukan !"
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'is_active' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            $response = [
                'errors' => $validator->errors(),
                'message' => "Validasi form gagal !"
            ];
            return response()->json($response, 422);
        }

        $count_template = LetterTemplate::where('letter_type', $template->letter_type)->isActive()->count();
        if ($request->is_active == false && $count_template == 1) {
            return response()->json([
                'errors' => [
                    'letter_type' => [
                        "Tidak dapat menonaktifkan template surat karena hanya tersisa satu !"
                    ]
                ],
                'message' => "Tidak dapat menonaktifkan template surat karena hanya tersisa satu !"
            ], 422);
        }

        $template->is_active = $request->is_active;
        $template->save();

        $status = $request->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json([
            'message' => "Template surat berhasil $status !",
            'data' => $template
        ]);
    }
}
