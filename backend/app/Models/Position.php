<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;
    protected $perPage = 10;

    public function scopeSearch(Builder $query, $search = '') : void {
        $query->where('name', 'like', '%'.$search.'%')->orWhere('type', 'like', '%'.$search.'%');
    }
}
