<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PertelaanPerjanjianKerjaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pendidikan' => $this->pendidikan,
            'tahun_satu' => $this->tahun_satu,
            'tunjangan_dasar_satu' => $this->tunjangan_dasar_satu,
            'tunjangan_fungsional_satu' => $this->tunjangan_fungsional_satu,
            'tunjangan_struktural_satu' => $this->tunjangan_struktural_satu,
            'tunjangan_kemahalan_satu' => $this->tunjangan_kemahalan_satu,
            'pendapatan_bulanan_satu' => $this->pendapatan_bulanan_satu,
            'tahun_dua' => $this->tahun_dua,
            'tunjangan_dasar_dua' => $this->tunjangan_dasar_dua,
            'tunjangan_fungsional_dua' => $this->tunjangan_fungsional_dua,
            'tunjangan_struktural_dua' => $this->tunjangan_struktural_dua,
            'tunjangan_kemahalan_dua' => $this->tunjangan_kemahalan_dua,
            'pendapatan_bulanan_dua' => $this->pendapatan_bulanan_dua,
            'fasilitas_lainnya' => json_decode($this->fasilitas_lainnya),
        ];
    }
}
