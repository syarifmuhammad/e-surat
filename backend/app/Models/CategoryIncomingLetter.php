<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryIncomingLetter extends Model
{
    use HasFactory;
    protected $perPage = 10;

    protected $fillable = [
        'name',
    ];

    public function incoming_letters()
    {
        return $this->hasMany(IncomingLetter::class, 'category_id', 'id');
    }

    public function scopeSearch($query, $val)
    {
        return $query->where('name', 'like', '%' . $val . '%');
    }
}
