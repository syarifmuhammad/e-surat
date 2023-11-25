<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterTemplate extends Model
{
    use HasFactory;

    // public static $DEFAULT = [
    //     "SURAT_KETERANGAN_KERJA" => [
    //         "letter_type" => "SURAT_KETERANGAN_KERJA",
    //         "file" => "b81f3481-0d57-448e-817a-8d46d2af0565.docx",
    //     ],
    // ];

    // public static function get_letter_template($id) {
    //     $format = self::where([
    //         "letter_type" => $letter_type,
    //     ])->orderBy('id')->first();

    //     if(!$format) {
    //         if (!array_key_exists($letter_type, self::$DEFAULT)) {
    //             return null;
    //         }
    //         return self::$DEFAULT[$letter_type];
    //     }

    //     return $format;
    // }

}
