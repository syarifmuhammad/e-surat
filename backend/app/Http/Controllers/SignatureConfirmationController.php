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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class SignatureConfirmationController extends Controller
{
    public function confirm_signature(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'data' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Tanda Tangan Tidak Valid',
                'errors' => $validate->errors(),
            ], 422);
        }

        try {
            $decrypt = Crypt::decrypt($request->data);
            if (!isset($decrypt['letter_type'])) {
                throw new \Exception('Tanda Tangan Tidak Valid');
            }

            switch ($decrypt['letter_type']) {
                case 'surat_keterangan_kerja':
                    $letter = SuratKeteranganKerja::find($decrypt['letter']);
                    break;
                case 'surat_keputusan_rotasi_kepegawaian':
                    $letter = SuratKeputusanRotasiKepegawaian::find($decrypt['letter']);
                    break;
                case 'surat_keputusan_pemberhentian':
                    $letter = SuratKeputusanPemberhentian::find($decrypt['letter']);
                    break;
                case 'surat_keputusan_pengangkatan':
                    $letter = SuratKeputusanPengangkatan::find($decrypt['letter']);
                    break;
                case 'surat_keputusan_pemberhentian_dan_pengangkatan':
                    $letter = SuratKeputusanPemberhentianDanPengangkatan::find($decrypt['letter']);
                    break;
                case 'surat_perjanjian_kerja_magang':
                    $letter = SuratPerjanjianKerjaMagang::find($decrypt['letter']);
                    break;
                case 'surat_perjanjian_kerja_dosen_luar_biasa':
                    $letter = SuratPerjanjianKerjaDosenLuarBiasa::find($decrypt['letter']);
                    break;
                case 'surat_perjanjian_kerja_dosen_full_time':
                    $letter = SuratPerjanjianKerjaDosenFullTime::find($decrypt['letter']);
                    break;
                default:
                    throw new \Exception('Tanda Tangan Tidak Valid');
                    break;
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
                'message' => 'Tanda Tangan Tidak Valid',
                'errors' => $e->getMessage(),
            ], 422);
        }
    }
}
