<?php

namespace App\Http\Controllers;

use App\Models\SuratKeteranganKerja;
use App\Models\SuratKeputusanRotasiKepegawaian;
use App\Models\SuratKeputusanPemberhentian;
use App\Models\SuratKeputusanPengangkatan;
use App\Models\SuratKeputusanPemberhentianDanPengangkatan;
use App\Models\SuratPerjanjianKerjaMagang;
use App\Models\SuratPerjanjianKerjaDosenLuarBiasa;
use App\Models\SuratPerjanjianKerjaDosenFullTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class SignatureConfirmationController extends Controller
{
    public function verify(Request $request, $token)
    {
        try {
            $decrypt = Crypt::decrypt($token);
            if (!isset($decrypt['letter_type'])) {
                throw new \Exception('Tanda Tangan Tidak Valid');
            }

            $valid_until_date = "";

            switch ($decrypt['letter_type']) {
                case 'surat_keterangan_kerja':
                    $letter = SuratKeteranganKerja::with('signers', 'signers.employee')->find($decrypt['letter']);
                    if ($letter->masa_berlaku != 0) {
                        $masa_berlaku = interval_to_array($letter->masa_berlaku);
                        $start_date = $letter->signers->sortBy('updated_at')->last();
                        $valid_until_date = Carbon::parse($start_date['updated_at'])->addYears($masa_berlaku['year'])->addMonths($masa_berlaku['month'])->addDays($masa_berlaku['day']);
                    }
                    break;
                case 'surat_keputusan_rotasi_kepegawaian':
                    $letter = SuratKeputusanRotasiKepegawaian::with('signers', 'signers.employee')->find($decrypt['letter']);
                    if ($letter->masa_berlaku != 0) {
                        $masa_berlaku = interval_to_array($letter->masa_berlaku);
                        $start_date = $letter->signers->sortBy('updated_at')->last();
                        $valid_until_date = Carbon::parse($start_date['updated_at'])->addYears($masa_berlaku['year'])->addMonths($masa_berlaku['month'])->addDays($masa_berlaku['day']);
                    }
                    break;
                case 'surat_keputusan_pemberhentian':
                    $letter = SuratKeputusanPemberhentian::with('signers', 'signers.employee')->find($decrypt['letter']);
                    if ($letter->masa_berlaku != 0) {
                        $masa_berlaku = interval_to_array($letter->masa_berlaku);
                        $start_date = $letter->signers->sortBy('updated_at')->last();
                        $valid_until_date = Carbon::parse($start_date['updated_at'])->addYears($masa_berlaku['year'])->addMonths($masa_berlaku['month'])->addDays($masa_berlaku['day']);
                    }
                    break;
                case 'surat_keputusan_pengangkatan':
                    $letter = SuratKeputusanPengangkatan::with('signers', 'signers.employee')->find($decrypt['letter']);
                    if ($letter->masa_berlaku != 0) {
                        $masa_berlaku = interval_to_array($letter->masa_berlaku);
                        $start_date = $letter->signers->sortBy('updated_at')->last();
                        $valid_until_date = Carbon::parse($start_date['updated_at'])->addYears($masa_berlaku['year'])->addMonths($masa_berlaku['month'])->addDays($masa_berlaku['day']);
                    }
                    break;
                case 'surat_keputusan_pemberhentian_dan_pengangkatan':
                    $letter = SuratKeputusanPemberhentianDanPengangkatan::with('signers', 'signers.employee')->find($decrypt['letter']);
                    if ($letter->masa_berlaku != 0) {
                        $masa_berlaku = interval_to_array($letter->masa_berlaku);
                        $start_date = $letter->signers->sortBy('updated_at')->last();
                        $valid_until_date = Carbon::parse($start_date['updated_at'])->addYears($masa_berlaku['year'])->addMonths($masa_berlaku['month'])->addDays($masa_berlaku['day']);
                    }
                    break;
                case 'surat_perjanjian_kerja_magang':
                    $letter = SuratPerjanjianKerjaMagang::with('signers', 'signers.employee')->find($decrypt['letter']);
                    if ($letter->masa_berlaku != 0) {
                        $masa_berlaku = interval_to_array($letter->masa_berlaku);
                        $start_date = $letter->mulai_berlaku;
                        $valid_until_date = Carbon::parse($start_date['updated_at'])->addYears($masa_berlaku['year'])->addMonths($masa_berlaku['month'])->addDays($masa_berlaku['day']);
                    }
                    break;
                case 'surat_perjanjian_kerja_dosen_luar_biasa':
                    $letter = SuratPerjanjianKerjaDosenLuarBiasa::with('signers', 'signers.employee')->find($decrypt['letter']);
                    if ($letter->masa_berlaku != 0) {
                        $masa_berlaku = interval_to_array($letter->masa_berlaku);
                        $start_date = $letter->mulai_berlaku;
                        $valid_until_date = Carbon::parse($start_date['updated_at'])->addYears($masa_berlaku['year'])->addMonths($masa_berlaku['month'])->addDays($masa_berlaku['day']);
                    }
                    break;
                case 'surat_perjanjian_kerja_dosen_full_time':
                    $letter = SuratPerjanjianKerjaDosenFullTime::with('signers', 'signers.employee')->find($decrypt['letter']);
                    if ($letter->masa_berlaku != 0) {
                        $masa_berlaku = interval_to_array($letter->masa_berlaku);
                        $start_date = $letter->mulai_berlaku;
                        $valid_until_date = Carbon::parse($start_date['updated_at'])->addYears($masa_berlaku['year'])->addMonths($masa_berlaku['month'])->addDays($masa_berlaku['day']);
                    }
                    break;
                default:
                    throw new \Exception('Tanda Tangan Tidak Valid');
                    break;
            }

            if (!$letter) {
                throw new \Exception('Tanda Tangan Tidak Valid');
            }

            return response()->json([
                'message' => 'Tanda Tangan Valid',
                'data' => [
                    'is_private' => $letter->is_private,
                    'reference_number' => $letter->reference_number,
                    'valid_until_date' => $valid_until_date->translatedFormat('l, d F Y'),
                    'valid' =>  $valid_until_date->greaterThanOrEqualTo(Carbon::now()),
                    'signers' => $letter->signers->map(function ($signer) {
                        return [
                            'id' => $signer->id,
                            'name' => $signer->employee->name,
                            'timestamp' => $signer->updated_at ? Carbon::parse($signer->updated_at)->translatedFormat('l, d F Y h:i:s') : null,
                        ];
                    })
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Tanda Tangan Tidak Valid',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }
    public function file(Request $request, $token)
    {
        try {
            $decrypt = Crypt::decrypt($token);
            if (!isset($decrypt['letter_type'])) {
                throw new \Exception('Tanda Tangan Tidak Valid');
            }

            $valid_until_date = "";

            switch ($decrypt['letter_type']) {
                case 'surat_keterangan_kerja':
                    $letter = SuratKeteranganKerja::with('signers', 'signers.employee')->find($decrypt['letter']);
                    if ($letter->masa_berlaku != 0) {
                        $masa_berlaku = interval_to_array($letter->masa_berlaku);
                        $start_date = $letter->signers->sortBy('updated_at')->last();
                        $valid_until_date = Carbon::parse($start_date['updated_at'])->addYears($masa_berlaku['year'])->addMonths($masa_berlaku['month'])->addDays($masa_berlaku['day']);
                    }
                    break;
                case 'surat_keputusan_rotasi_kepegawaian':
                    $letter = SuratKeputusanRotasiKepegawaian::with('signers', 'signers.employee')->find($decrypt['letter']);
                    if ($letter->masa_berlaku != 0) {
                        $masa_berlaku = interval_to_array($letter->masa_berlaku);
                        $start_date = $letter->signers->sortBy('updated_at')->last();
                        $valid_until_date = Carbon::parse($start_date['updated_at'])->addYears($masa_berlaku['year'])->addMonths($masa_berlaku['month'])->addDays($masa_berlaku['day']);
                    }
                    break;
                case 'surat_keputusan_pemberhentian':
                    $letter = SuratKeputusanPemberhentian::with('signers', 'signers.employee')->find($decrypt['letter']);
                    if ($letter->masa_berlaku != 0) {
                        $masa_berlaku = interval_to_array($letter->masa_berlaku);
                        $start_date = $letter->signers->sortBy('updated_at')->last();
                        $valid_until_date = Carbon::parse($start_date['updated_at'])->addYears($masa_berlaku['year'])->addMonths($masa_berlaku['month'])->addDays($masa_berlaku['day']);
                    }
                    break;
                case 'surat_keputusan_pengangkatan':
                    $letter = SuratKeputusanPengangkatan::with('signers', 'signers.employee')->find($decrypt['letter']);
                    if ($letter->masa_berlaku != 0) {
                        $masa_berlaku = interval_to_array($letter->masa_berlaku);
                        $start_date = $letter->signers->sortBy('updated_at')->last();
                        $valid_until_date = Carbon::parse($start_date['updated_at'])->addYears($masa_berlaku['year'])->addMonths($masa_berlaku['month'])->addDays($masa_berlaku['day']);
                    }
                    break;
                case 'surat_keputusan_pemberhentian_dan_pengangkatan':
                    $letter = SuratKeputusanPemberhentianDanPengangkatan::with('signers', 'signers.employee')->find($decrypt['letter']);
                    if ($letter->masa_berlaku != 0) {
                        $masa_berlaku = interval_to_array($letter->masa_berlaku);
                        $start_date = $letter->signers->sortBy('updated_at')->last();
                        $valid_until_date = Carbon::parse($start_date['updated_at'])->addYears($masa_berlaku['year'])->addMonths($masa_berlaku['month'])->addDays($masa_berlaku['day']);
                    }
                    break;
                case 'surat_perjanjian_kerja_magang':
                    $letter = SuratPerjanjianKerjaMagang::with('signers', 'signers.employee')->find($decrypt['letter']);
                    if ($letter->masa_berlaku != 0) {
                        $masa_berlaku = interval_to_array($letter->masa_berlaku);
                        $start_date = $letter->mulai_berlaku;
                        $valid_until_date = Carbon::parse($start_date['updated_at'])->addYears($masa_berlaku['year'])->addMonths($masa_berlaku['month'])->addDays($masa_berlaku['day']);
                    }
                    break;
                case 'surat_perjanjian_kerja_dosen_luar_biasa':
                    $letter = SuratPerjanjianKerjaDosenLuarBiasa::with('signers', 'signers.employee')->find($decrypt['letter']);
                    if ($letter->masa_berlaku != 0) {
                        $masa_berlaku = interval_to_array($letter->masa_berlaku);
                        $start_date = $letter->mulai_berlaku;
                        $valid_until_date = Carbon::parse($start_date['updated_at'])->addYears($masa_berlaku['year'])->addMonths($masa_berlaku['month'])->addDays($masa_berlaku['day']);
                    }
                    break;
                case 'surat_perjanjian_kerja_dosen_full_time':
                    $letter = SuratPerjanjianKerjaDosenFullTime::with('signers', 'signers.employee')->find($decrypt['letter']);
                    if ($letter->masa_berlaku != 0) {
                        $masa_berlaku = interval_to_array($letter->masa_berlaku);
                        $start_date = $letter->mulai_berlaku;
                        $valid_until_date = Carbon::parse($start_date['updated_at'])->addYears($masa_berlaku['year'])->addMonths($masa_berlaku['month'])->addDays($masa_berlaku['day']);
                    }
                    break;
                default:
                    throw new \Exception('Tanda Tangan Tidak Valid');
                    break;
            }

            if (!$letter) {
                throw new \Exception('Tanda Tangan Tidak Valid');
            }

            if ($letter->is_private == true) {
                if (!auth()->check()) {
                    return response()->json([
                        'message' => 'Surat ini bersifat rahasia',
                        'errors' => 'Anda tidak memiliki akses untuk melihat surat ini',
                    ], 403);
                }
            }

            $fileNameServerDocx = "app/tmp/" . $decrypt['letter_type'] . "/" . $letter->id . '.docx';
            $templateProcessor = $letter->generate_docx();
            $templateProcessor->saveAs(storage_path($fileNameServerDocx));

            $response = Http::post(env('APP_DOCX_CONVERTER_URL') . '/convert', ['file_path' => $fileNameServerDocx]);
            if ($response->failed()) {
                return response()->json([
                    'errors' => "Something errors"
                ], 500);
            }

            $filename = $letter->id . '.pdf';
            $tmpFileNameServerPdf = 'app/tmp/'. $decrypt['letter_type'] .'/' . $filename;
            if ($response->successful() && file_exists(storage_path($tmpFileNameServerPdf))) {
                unlink(storage_path($fileNameServerDocx));
                return response()->file(storage_path($tmpFileNameServerPdf));
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'File tidak ditemukan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }
}
