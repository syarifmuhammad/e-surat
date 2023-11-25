<?php

namespace App\Http\Controllers;

use App\Models\ReferenceNumberSetting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use League\CommonMark\Reference\Reference;

class ReferenceNumberSettingController extends Controller
{
    public function get_letter_types()
    {
        $reference_number_settings = ReferenceNumberSetting::$DEFAULT;
        $get_reference_number_settings_from_db = ReferenceNumberSetting::all();
        foreach ($get_reference_number_settings_from_db as $r) {
            $reference_number_settings[$r->letter_type]['letter_type'] =  $r->letter_type;
            $reference_number_settings[$r->letter_type]['prefix'] =  $r->prefix;
            $reference_number_settings[$r->letter_type]['suffix'] =  $r->suffix;
        }
        $keys = array_keys($reference_number_settings);
        $letter_types = [];
        foreach ($keys as $key) {
            $letter_types[] = $reference_number_settings[$key];
        }
        return response()->json([
            "data" => $letter_types,
        ]);
    }

    public function update(Request $request)
    {
        $letter_types = ReferenceNumberSetting::$DEFAULT;
        $validate = Validator::make($request->all(), [
            "letter_type" => "required|string|in:" . implode(",", array_keys($letter_types)),
            "prefix" => "present",
            "suffix" => "present",
        ]);

        if ($validate->fails()) {
            $response = [
                'errors' => $validate->errors(),
                'message' => "Validasi form gagal !"
            ];
            return response()->json($response, 422);
        }

        try {
            ReferenceNumberSetting::updateOrInsert(
                ['letter_type' => $request->letter_type],
                [
                    'prefix' => $request->prefix ?? "",
                    'suffix' => $request->suffix ?? "",
                    'created_at' => now()
                ]
            );
            return response()->json([
                'message' => 'Berhasil menyimpan pengaturan nomor surat !',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
