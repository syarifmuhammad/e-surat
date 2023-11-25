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
            'nip' => $this->nip,
            'name' => $this->name,
            'email' => $this->email,
            'positions' => $this->positions->pluck('position'),
            'roles' => $this->account ? $this->account->roles : 'pegawai',
            'is_registered' => $this->isRegistered(),
        ];
    }
}
