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

class AnalyticsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $surat_keterangan_kerja = SuratKeteranganKerja::byUser()->count();
        $surat_keterangan_kerja_need_reference_number = SuratKeteranganKerja::byUser()->where('reference_number', null)->count();
        $surat_keterangan_kerja_need_signed = SuratKeteranganKerja::byUser()->whereNotSigned()->count();
        $sk_rotasi_kepegawaian = SuratKeputusanRotasiKepegawaian::byUser()->count();
        $sk_rotasi_kepegawaian_need_reference_number = SuratKeputusanRotasiKepegawaian::byUser()->where('reference_number', null)->count();
        $sk_rotasi_kepegawaian_need_signed = SuratKeputusanRotasiKepegawaian::byUser()->whereNotSigned()->count();
        $sk_pemberhentian = SuratKeputusanPemberhentian::byUser()->count();
        $sk_pemberhentian_need_reference_number = SuratKeputusanPemberhentian::byUser()->where('reference_number', null)->count();
        $sk_pemberhentian_need_signed = SuratKeputusanPemberhentian::byUser()->whereNotSigned()->count();
        $sk_pengangkatan = SuratKeputusanPengangkatan::byUser()->count();
        $sk_pengangkatan_need_reference_number = SuratKeputusanPengangkatan::byUser()->where('reference_number', null)->count();
        $sk_pengangkatan_need_signed = SuratKeputusanPengangkatan::byUser()->whereNotSigned()->count();
        $sk_pemberhentian_dan_pengangkatan = SuratKeputusanPemberhentianDanPengangkatan::byUser()->count();
        $sk_pemberhentian_dan_pengangkatan_need_reference_number = SuratKeputusanPemberhentianDanPengangkatan::byUser()->where('reference_number', null)->count();
        $sk_pemberhentian_dan_pengangkatan_need_signed = SuratKeputusanPemberhentianDanPengangkatan::byUser()->whereNotSigned()->count();
        $spk_magang = SuratPerjanjianKerjaMagang::byUser()->count();
        $spk_magang_need_reference_number = SuratPerjanjianKerjaMagang::byUser()->where('reference_number', null)->count();
        $spk_magang_need_signed = SuratPerjanjianKerjaMagang::byUser()->whereNotSigned()->count();
        $spk_dosen_luar_biasa = SuratPerjanjianKerjaDosenLuarBiasa::byUser()->count();
        $spk_dosen_luar_biasa_need_reference_number = SuratPerjanjianKerjaDosenLuarBiasa::byUser()->where('reference_number', null)->count();
        $spk_dosen_luar_biasa_need_signed = SuratPerjanjianKerjaDosenLuarBiasa::byUser()->whereNotSigned()->count();
        $spk_dosen_full_time = SuratPerjanjianKerjaDosenFullTime::byUser()->count();
        $spk_dosen_full_time_need_reference_number = SuratPerjanjianKerjaDosenFullTime::byUser()->where('reference_number', null)->count();
        $spk_dosen_full_time_need_signed = SuratPerjanjianKerjaDosenFullTime::byUser()->whereNotSigned()->count();
        return response()->json([
            "surat_keterangan_kerja" => [
                "count" => $surat_keterangan_kerja,
                "need_reference_number" => $surat_keterangan_kerja_need_reference_number,
                "need_signed" => $surat_keterangan_kerja_need_signed,
            ],
            "sk_rotasi_kepegawaian" => [
                "count" => $sk_rotasi_kepegawaian,
                "need_reference_number" => $sk_rotasi_kepegawaian_need_reference_number,
                "need_signed" => $sk_rotasi_kepegawaian_need_signed,
            ],
            "sk_pemberhentian" => [
                "count" => $sk_pemberhentian,
                "need_reference_number" => $sk_pemberhentian_need_reference_number,
                "need_signed" => $sk_pemberhentian_need_signed,
            ],
            "sk_pengangkatan" => [
                "count" => $sk_pengangkatan,
                "need_reference_number" => $sk_pengangkatan_need_reference_number,
                "need_signed" => $sk_pengangkatan_need_signed,
            ],
            "sk_pemberhentian_dan_pengangkatan" => [
                "count" => $sk_pemberhentian_dan_pengangkatan,
                "need_reference_number" => $sk_pemberhentian_dan_pengangkatan_need_reference_number,
                "need_signed" => $sk_pemberhentian_dan_pengangkatan_need_signed,
            ],
            "spk_magang" => [
                "count" => $spk_magang,
                "need_reference_number" => $spk_magang_need_reference_number,
                "need_signed" => $spk_magang_need_signed,
            ],
            "spk_dosen_luar_biasa" => [
                "count" => $spk_dosen_luar_biasa,
                "need_reference_number" => $spk_dosen_luar_biasa_need_reference_number,
                "need_signed" => $spk_dosen_luar_biasa_need_signed,
            ],
            "spk_dosen_full_time" => [
                "count" => $spk_dosen_full_time,
                "need_reference_number" => $spk_dosen_full_time_need_reference_number,
                "need_signed" => $spk_dosen_full_time_need_signed,
            ],
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
