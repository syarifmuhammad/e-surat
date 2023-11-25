<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $primaryKey = 'nip';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $perPage = 10;

    public function scopeSearch($query, $search = '')
    {
        $query->where('nip', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%')
            ->orWhere('name', 'like', '%' . $search . '%');
    }

    public function scopeExceptSuperAdmin($query)
    {
        $query->whereHas('account', function ($query) {
            return $query->whereNot('roles', 'superadmin');
        })->orWhereDoesntHave('account');
    }

    public function positions()
    {
        return $this->hasMany(EmployeePosition::class, 'nip', 'nip');
    }

    public function account()
    {
        return $this->hasOne(User::class, 'nip', 'nip');
    }

    public function isRegistered()
    {
        return $this->account()->exists();
    }
}
