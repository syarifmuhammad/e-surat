<?php

namespace App\Http\Controllers;

use App\Models\LetterTemplate;
use App\Models\ReferenceNumberSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LetterTemplateController extends Controller
{
    public function index(Request $request)
    {
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
}
