<?php

namespace App\Http\Controllers;

use App\Models\SuratKeputusanPemberhentian;
use App\Models\SuratKeputusanPemberhentianDanPengangkatan;
use App\Models\SuratKeputusanPengangkatan;
use App\Models\SuratKeputusanRotasiKepegawaian;
use App\Models\SuratKeteranganKerja;
use App\Models\SuratPerjanjianKerjaDosenFullTime;
use App\Models\SuratPerjanjianKerjaDosenLuarBiasa;
use App\Models\SuratPerjanjianKerjaMagang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function report_outcoming_letter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "bulan" => "required|date_format:Y-m"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $date = explode('-', $request->bulan);
        $bulan = $date[1];
        $tahun = $date[0];
        $reports = [];
        $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $summary = [
            'surat_keterangan_kerja' => 0,
            'sk_rotasi_kepegawaian' => 0,
            'sk_pemberhentian' => 0,
            'sk_pengangkatan' => 0,
            'sk_pemberhentian_dan_pengangkatan' => 0,
            'spk_magang' => 0,
            'spk_dosen_luar_biasa' => 0,
            'spk_dosen_full_time' => 0,
        ];
        for ($i = 0; $i < $jumlah_hari; $i++) {
            $tanggal = $tahun . '-' . $bulan . '-' . str_pad($i+1, 2, '0', STR_PAD_LEFT);
            $surat_keterangan_kerja = SuratKeteranganKerja::whereDate('tanggal_surat', $tanggal)->whereSigned()->count();
            $sk_rotasi_kepegawaian = SuratKeputusanRotasiKepegawaian::whereDate('tanggal_surat', $tanggal)->whereSigned()->count();
            $sk_pemberhentian = SuratKeputusanPemberhentian::whereDate('tanggal_surat', $tanggal)->whereSigned()->count();
            $sk_pengangkatan = SuratKeputusanPengangkatan::whereDate('tanggal_surat', $tanggal)->whereSigned()->count();
            $sk_pemberhentian_dan_pengangkatan = SuratKeputusanPemberhentianDanPengangkatan::whereDate('tanggal_surat', $tanggal)->whereSigned()->count();
            $spk_magang = SuratPerjanjianKerjaMagang::whereDate('tanggal_surat', $tanggal)->whereSigned()->count();
            $spk_dosen_luar_biasa = SuratPerjanjianKerjaDosenLuarBiasa::whereDate('tanggal_surat', $tanggal)->whereSigned()->count();
            $spk_dosen_full_time = SuratPerjanjianKerjaDosenFullTime::whereDate('tanggal_surat', $tanggal)->whereSigned()->count();
            $reports[] = [
                'tanggal' => $tanggal,
                'surat_keterangan_kerja' => $surat_keterangan_kerja,
                'sk_rotasi_kepegawaian' => $sk_rotasi_kepegawaian,
                'sk_pemberhentian' => $sk_pemberhentian,
                'sk_pengangkatan' => $sk_pengangkatan,
                'sk_pemberhentian_dan_pengangkatan' => $sk_pemberhentian_dan_pengangkatan,
                'spk_magang' => $spk_magang,
                'spk_dosen_luar_biasa' => $spk_dosen_luar_biasa,
                'spk_dosen_full_time' => $spk_dosen_full_time,
            ];
            foreach($summary as $key => $s) {
                $summary[$key] += ${$key};
            }
        }

        return response()->json([
            'data' => [
                'details' => $reports,
                'summary' => $summary,
            ]
        ], 200);
    }
}
