<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReferenceNumberSetting extends Model
{
    use HasFactory;

    protected $primaryKey = 'letter_type';
    protected $keyType = 'string';
    public $timestamps = true;

    public static $DEFAULT = [
        "SURAT_KETERANGAN_KERJA" => [
            "name" => "Surat Keterangan Kerja",
            "letter_type" => "SURAT_KETERANGAN_KERJA",
            "prefix" => "",
            "suffix" => "/SDM11/WRII/{bln}/{thn}",
        ],
        "SURAT_KEPUTUSAN_ROTASI_KEPEGAWAIAN" => [
            "name" => "Surat Keputusan Rotasi Kepegawaian",
            "letter_type" => "SURAT_KEPUTUSAN_ROTASI_KEPEGAWAIAN",
            "prefix" => "",
            "suffix" => "/SDM6/WRII/{bln}/{thn}",
        ],
        "SURAT_KEPUTUSAN_PEMBERHENTIAN" => [
            "name" => "Surat Keputusan Pemberhentian",
            "letter_type" => "SURAT_KEPUTUSAN_PEMBERHENTIAN",
            "prefix" => "",
            "suffix" => "/SDM6/WRII/{bln}/{thn}",
        ],
        "SURAT_KEPUTUSAN_PENGANGKATAN" => [
            "name" => "Surat Keputusan Pengangkatan",
            "letter_type" => "SURAT_KEPUTUSAN_PENGANGKATAN",
            "prefix" => "",
            "suffix" => "/SDM6/WRII/{bln}/{thn}",
        ],
        "SURAT_KEPUTUSAN_PEMBERHENTIAN_DAN_PENGANGKATAN" => [
            "name" => "Surat Keputusan Pemberhentian Dan Pengangkatan",
            "letter_type" => "SURAT_KEPUTUSAN_PEMBERHENTIAN_DAN_PENGANGKATAN",
            "prefix" => "",
            "suffix" => "/SDM6/WRII/{bln}/{thn}",
        ],
        "SURAT_PERJANJIAN_KERJA_MAGANG" => [
            "name" => "Surat Perjanjian Kerja Magang",
            "letter_type" => "SURAT_PERJANJIAN_KERJA_MAGANG",
            "prefix" => "",
            "suffix" => "/SDM24/WRII/{bln}/{thn}",
        ],
        "SURAT_PERJANJIAN_KERJA_DOSEN_LUAR_BIASA" => [
            "name" => "Surat Perjanjian Kerja Dosen Luar Biasa",
            "letter_type" => "SURAT_PERJANJIAN_KERJA_DOSEN_LUAR_BIASA",
            "prefix" => "",
            "suffix" => "/SDM16/WRII/{bln}/{thn}",
        ],
        "SURAT_PERJANJIAN_KERJA_DOSEN_FULL_TIME" => [
            "name" => "Surat Perjanjian Kerja Dosen Full Time",
            "letter_type" => "SURAT_PERJANJIAN_KERJA_DOSEN_FULL_TIME",
            "prefix" => "",
            "suffix" => "/SDM16/WRII/{bln}/{thn}",
        ],
        // "DAMIU" => [
        //     "letter_type" => "DAMIU",
        //     "prefix" => "540.2/",
        //     "suffix" => "/PLSi",
        // ],
        // "DISPENSASI_NIKAH" => [
        //     "letter_type" => "DISPENSASI_NIKAH",
        //     "prefix" => "474.2/",
        //     "suffix" => "/PLSi",
        // ],
        // "SKTM_SEKOLAH" => [
        //     "letter_type" => "SKTM_SEKOLAH",
        //     "prefix" => "476/",
        //     "suffix" => "/PLSi",
        // ],
        // "SKTM_DTKS" => [
        //     "letter_type" => "SKTM_DTKS",
        //     "prefix" => "460/",
        //     "suffix" => "/PLSi",
        // ],
        // "BIODATA_PENDUDUK_WNI" => [
        //     "letter_type" => "BIODATA_PENDUDUK_WNI",
        //     "prefix" => "F-1/",
        //     "suffix" => "",
        // ],
        // "SKPWNI" => [
        //     "letter_type" => "SKPWNI",
        //     "prefix" => "F-1.{bln}.",
        //     "suffix" => "",
        // ],
    ];

    protected $fillable = [
        "letter_type",
        "prefix",
        "suffix",
    ];

    public static function generate_dummy($name)
    {
        $reference_number = self::$DEFAULT[$name];
        if (!$reference_number) {
            return null;
        }
        $reference_number = $reference_number['prefix'] . 'XXX' . $reference_number['suffix'];
        $reference_number = str_replace("{tgl}", 'XX', $reference_number);
        $reference_number = str_replace("{bln}", 'XX', $reference_number);
        $reference_number = str_replace("{thn}", 'XXXX', $reference_number);
        return $reference_number;
    }

    public static function get_reference_number($letter_type)
    {
        $format = self::where([
            "letter_type" => $letter_type,
        ])->first();

        if (!$format) {
            if (!array_key_exists($letter_type, self::$DEFAULT)) {
                return null;
            }
            return self::$DEFAULT[$letter_type];
        }

        return $format;
    }


    // Parse {tgl} {bln} {thn} ke tanggal, bulan, dan tahun saat ini
    public static function parse_reference_number($reference_number)
    {
        $reference_number = str_replace("{tgl}", sprintf("%02s", Carbon::now()->day), $reference_number);
        $reference_number = str_replace("{bln}", sprintf("%02s", Carbon::now()->month), $reference_number);
        $reference_number = str_replace("{thn}", Carbon::now()->year, $reference_number);
        return $reference_number;
    }

    // Parse {tgl} {bln} {thn} ke tanggal, bulan, dan tahun yang diinginkan
    public static function parse_reference_number_with_date($reference_number, $date)
    {
        $reference_number = str_replace("{tgl}", sprintf("%02s", Carbon::parse($date)->day), $reference_number);
        $reference_number = str_replace("{bln}", sprintf("%02s", Carbon::parse($date)->month), $reference_number);
        $reference_number = str_replace("{thn}", Carbon::parse($date)->year, $reference_number);
        return $reference_number;
    }

    public static function get_and_parse_reference_number_with_date($letter_type, $order_number, $date)
    {
        $reference_number = ReferenceNumberSetting::where('letter_type', $letter_type)->first();
        if (!$reference_number) {
            $reference_number = self::$DEFAULT[$letter_type];
        }
        $order_number_len = strlen((string) $order_number) > 3 ? strlen((string) $order_number) : 3;
        $reference_number = $reference_number['prefix'] . str_pad($order_number, $order_number_len, '0', STR_PAD_LEFT) . $reference_number['suffix'];
        return self::parse_reference_number_with_date($reference_number, $date);
    }
}
