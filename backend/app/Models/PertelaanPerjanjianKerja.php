<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PertelaanPerjanjianKerja extends Model
{
    use HasFactory;

    protected $table = 'pertelaan_perjanjian_kerja';

    public $incrementing = false;

    public function surat_dosen_full_time()
    {
        return $this->belongsTo(SuratPerjanjianKerjaDosenFullTime::class, 'surat_dosen_full_time_id', 'id');
    }
}
