<?php

namespace App\Http\Resources;

use App\Models\ReferenceNumberSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\SuratKeputusanRotasiKepegawaian as Letter;
use Carbon\Carbon;

class SuratKeputusanRotasiKepegawaianResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $reference_number = $this->reference_number;
        if ($reference_number == null) {
            $reference_number = ReferenceNumberSetting::generate_dummy(Letter::NAME);
        }

        if (!$this->have_reference_number()) {
            $status = 'waiting_for_reference_number';
        } else if ($this->have_reference_number() && !$this->is_signed()) {
            $status = 'waiting_for_signed';
        } else if ($this->is_signed()) {
            $status = 'signed';
        }
        return [
            'id' => $this->id,
            'reference_number' => $reference_number,
            'employee' => [
                'id' => $this->employee->id,
                'nip' => $this->employee->nip,
                'name' => $this->employee->name,
                'status_awal' => $this->status_awal,
                'jabatan_awal' => $this->jabatan_awal,
                'status_akhir' => $this->status_akhir,
                'jabatan_akhir' => $this->jabatan_akhir,
                'positions' => $this->employee->positions->pluck('position'),
            ],
            'signer' => [
                'id' => $this->signer->id,
                'nip' => $this->signer->nip,
                'name' => $this->signer->name,
                'position' => $this->signer_position,
                'positions' => $this->signer->positions->pluck('position'),
            ],
            'nomor_berita_acara' => $this->nomor_berita_acara,
            'tanggal_berita_acara' => $this->tanggal_berita_acara,
            'tanggal_berlaku' => $this->tanggal_berlaku,
            'tanggal_berlaku' => $this->tanggal_berlaku,
            'have_reference_number' => $this->have_reference_number(),
            'can_give_reference_number' => $this->can_give_reference_number(),
            'can_signed' => $this->can_signed(),
            'can_edit' => $this->can_edit(),
            'can_upload_verified_file' => $this->can_upload_verified_file(),
            'signature_type' => $this->signature_type,
            'is_signed' => $this->is_signed(),
            'status' => $status,
            'created_at' => Carbon::parse($this->created_at)->translatedFormat('l, d F Y'),
        ];
    }
}
