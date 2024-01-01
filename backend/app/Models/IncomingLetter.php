<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingLetter extends Model
{
    use HasFactory;
    protected $perPage = 10;

    public function scopeSearch($query, $search)
    {
        return $query->where('reference_number', 'like', '%' . $search . '%')
            ->orWhere('asal_surat', 'like', '%' . $search . '%')
            ->orWhere('perihal', 'like', '%' . $search . '%')
            ->orWhere('tanggal_surat', 'like', '%' . $search . '%')
            ->orWhere('ocr_text', 'like', '%' . $search . '%')
            ->orWhere('keterangan', 'like', '%' . $search . '%');
    }

    public function category()
    {
        return $this->belongsTo(CategoryIncomingLetter::class, 'category_id');
    }
}
