<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $perPage = 10;

    protected $table = 'unit';

    public function scopeSearch($query, $search = '')
    {
        $query->where('nama', 'like', '%' . $search . '%')
            ->orWhere('singkatan', 'like', '%' . $search . '%');
    }
}
