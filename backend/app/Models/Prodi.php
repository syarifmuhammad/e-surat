<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;

    protected $table = 'prodi';

    public function scopeSearch($query, $search = '')
    {
        $query->where('nama_prodi', 'like', '%' . $search . '%')
            ->orWhere('singkatan_prodi', 'like', '%' . $search . '%')
            ->orWhere('nama_fakultas', 'like', '%' . $search . '%')
            ->orWhere('singkatan_fakultas', 'like', '%' . $search . '%');
    }
}
