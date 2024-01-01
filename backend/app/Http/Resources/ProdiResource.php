<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdiResource extends JsonResource
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
            'nama_prodi' => $this->nama_prodi,
            'singkatan_prodi' => $this->singkatan_prodi,
            'nama_fakultas' => $this->nama_fakultas,
            'singkatan_fakultas' => $this->singkatan_fakultas,
        ];
    }
}
