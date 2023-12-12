<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncomingLetterResource extends JsonResource
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
            'category' => new CategoryIncomingLetterResource($this->category),
            'reference_number' => $this->reference_number,
            'asal_surat' => $this->asal_surat,
            'perihal' => $this->perihal,
            'keterangan' => substr($this->keterangan, 0, 100),
            'tanggal_surat_formated' => Carbon::parse($this->tanggal_surat)->translatedFormat('l, d F Y'),
            'tanggal_surat' => $this->tanggal_surat,
        ];
    }
}
