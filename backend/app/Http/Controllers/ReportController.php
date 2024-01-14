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
use Barryvdh\DomPDF\Facade\Pdf;

use function Ramsey\Uuid\v1;

class ReportController extends Controller
{

    private function getLetterName($jenis_surat) {
        switch ($jenis_surat) {
            case 'surat_keterangan_kerja':
                $nama_surat = "Surat Keterangan Kerja";
                break;
            case 'sk_rotasi_kepegawaian':
                $nama_surat = "Surat Keputusan Rotasi Kepegawaian";
                break;
            case 'sk_pemberhentian':
                $nama_surat = "Surat Keputusan Pemberhentian";
                break;
            case 'sk_pengangkatan':
                $nama_surat = "Surat Keputusan Pengangkatan";
                break;
            case 'sk_pemberhentian_dan_pengangkatan':
                $nama_surat = "Surat Keputusan Pemberhentian dan Pengangkatan";
                break;
            case 'spk_magang':
                $nama_surat = "Surat Perjanjian Kerja Magang";
                break;
            case 'spk_dosen_luar_biasa':
                $nama_surat = "Surat Perjanjian Kerja Dosen Luar Biasa";
                break;
            case 'spk_dosen_full_time':
                $nama_surat = "Surat Perjanjian Kerja Dosen Full Time";
                break;
            default:
                $nama_surat = "";
                break;
        }
        return $nama_surat;
    }

    private function prepareReport($jenis_surat, $start_date, $end_date)
    {
        $reports = [];
        $summary = [];
        for ($tanggal = $start_date; $tanggal <= $end_date; $tanggal = date('Y-m-d', strtotime($tanggal . ' +1 day'))) {
            $report = [
                'tanggal' => $tanggal,
            ];

            if ($jenis_surat == 'surat_keterangan_kerja' || $jenis_surat == 'semua_surat') {
                $surat_keterangan_kerja = SuratKeteranganKerja::whereDate('tanggal_surat', $tanggal)->whereSigned()->count();
                $report[$this->getLetterName('surat_keterangan_kerja')] = $surat_keterangan_kerja;
            }
            if ($jenis_surat == 'sk_rotasi_kepegawaian' || $jenis_surat == 'semua_surat') {
                $sk_rotasi_kepegawaian = SuratKeputusanRotasiKepegawaian::whereDate('tanggal_surat', $tanggal)->whereSigned()->count();
                $report[$this->getLetterName('sk_rotasi_kepegawaian')] = $sk_rotasi_kepegawaian;
            }
            if ($jenis_surat == 'sk_pemberhentian' || $jenis_surat == 'semua_surat') {
                $sk_pemberhentian = SuratKeputusanPemberhentian::whereDate('tanggal_surat', $tanggal)->whereSigned()->count();
                $report[$this->getLetterName('sk_pemberhentian')] = $sk_pemberhentian;
            }
            if ($jenis_surat == 'sk_pengangkatan' || $jenis_surat == 'semua_surat') {
                $sk_pengangkatan = SuratKeputusanPengangkatan::whereDate('tanggal_surat', $tanggal)->whereSigned()->count();
                $report[$this->getLetterName('sk_pengangkatan')] = $sk_pengangkatan;
            }
            if ($jenis_surat == 'sk_pemberhentian_dan_pengangkatan' || $jenis_surat == 'semua_surat') {
                $sk_pemberhentian_dan_pengangkatan = SuratKeputusanPemberhentianDanPengangkatan::whereDate('tanggal_surat', $tanggal)->whereSigned()->count();
                $report[$this->getLetterName('sk_pemberhentian_dan_pengangkatan')] = $sk_pemberhentian_dan_pengangkatan;
            }
            if ($jenis_surat == 'spk_magang' || $jenis_surat == 'semua_surat') {
                $spk_magang = SuratPerjanjianKerjaMagang::whereDate('tanggal_surat', $tanggal)->whereSigned()->count();
                $report[$this->getLetterName('spk_magang')] = $spk_magang;
            }
            if ($jenis_surat == 'spk_dosen_luar_biasa' || $jenis_surat == 'semua_surat') {
                $spk_dosen_luar_biasa = SuratPerjanjianKerjaDosenLuarBiasa::whereDate('tanggal_surat', $tanggal)->whereSigned()->count();
                $report[$this->getLetterName('spk_dosen_luar_biasa')] = $spk_dosen_luar_biasa;
            }
            if ($jenis_surat == 'spk_dosen_full_time' || $jenis_surat == 'semua_surat') {
                $spk_dosen_full_time = SuratPerjanjianKerjaDosenFullTime::whereDate('tanggal_surat', $tanggal)->whereSigned()->count();
                $report[$this->getLetterName('spk_dosen_full_time')] = $spk_dosen_full_time;
            }
            $reports[] = $report;

            if ($jenis_surat == 'semua_surat') {
                foreach ($report as $key => $value) {
                    if ($key != 'tanggal') {
                        if (isset($summary[$key])) {
                            $summary[$key] += $value;
                        } else {
                            $summary[$key] = $value;
                        }
                    }
                }
            } else {
                if (isset($summary[$this->getLetterName($jenis_surat)])) {
                    $summary[$this->getLetterName($jenis_surat)] += ${$jenis_surat} ?? 0;
                } else {
                    $summary[$this->getLetterName($jenis_surat)] = ${$jenis_surat} ?? 0;
                }
            }
        }

        $nama_surat = $this->getLetterName($jenis_surat) != "" ? $this->getLetterName($jenis_surat) : "Surat Keluar";
        $nama_file = "Laporan " . $nama_surat . " " . date('d-m-Y', strtotime($start_date)) . ' s.d ' . date('d-m-Y', strtotime($end_date));
        return ['reports' => $reports, 'summary' => $summary, 'start_date' => $start_date, 'end_date' => $end_date, 'title' => $nama_file, 'nama_surat' => $nama_surat];
    }

    public function report_outcoming_letter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "jenis_surat" => "required|in:semua_surat,surat_keterangan_kerja,sk_rotasi_kepegawaian,sk_pemberhentian,sk_pengangkatan,sk_pemberhentian_dan_pengangkatan,spk_magang,spk_dosen_luar_biasa,spk_dosen_full_time",
            "start_date" => "required|date",
            "end_date" => "required|date|after_or_equal:start_date",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $report = $this->prepareReport($request->jenis_surat, $request->start_date, $request->end_date);
        $pdf = Pdf::loadView('pdf.report', $report);
        return $pdf->download($report['title'] . ".pdf");
    }


    public function helper_to_check_without_download(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "jenis_surat" => "required|in:semua_surat,surat_keterangan_kerja,sk_rotasi_kepegawaian,sk_pemberhentian,sk_pengangkatan,sk_pemberhentian_dan_pengangkatan,spk_magang,spk_dosen_luar_biasa,spk_dosen_full_time",
            "start_date" => "required|date",
            "end_date" => "required|date|after_or_equal:start_date",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $report = $this->prepareReport($request->jenis_surat, $request->start_date, $request->end_date);
        return view('pdf.report', $report);
    }
}
