<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'nip' => $this->nip,
            'nik' => $this->nik,
            'name' => $this->name,
            'email' => $this->email,
            'rekening' => $this->rekening,
            'positions' => $this->positions->pluck('position'),
            'jabatan_fungsional' => $this->positions->where('position.type', 'fungsional')->pluck('position'),
            'roles' => $this->account ? $this->account->roles : 'pegawai',
            'profesi' => $this->profesi,
            'is_registered' => $this->isRegistered(),
        ];
    }
}
