<?php

namespace App\Http\Resources;

use App\Models\ReferenceNumberSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\SuratPerjanjianKerjaMagang as Letter;
use Carbon\Carbon;

class SuratPerjanjianKerjaMagangResource extends JsonResource
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
            'letter_template_id' => $this->letter_template_id,
            'reference_number' => $reference_number,
            'employee' => [
                'id' => $this->employee->id,
                'nik' => $this->employee->nik,
                'nip' => $this->employee->nip,
                'name' => $this->employee->name,
                'position' => $this->position,
                'positions' => $this->employee->positions->pluck('position'),
            ],
            'rekening' => json_decode($this->rekening),
            'mulai_berlaku' => $this->mulai_berlaku,
            'akhir_berlaku' => $this->akhir_berlaku,
            'tempat_kerja' => $this->tempat_kerja,
            'tugas' => json_decode($this->tugas),
            'upah' => $this->upah,
            'penanggung_pembayaran' => $this->penanggung_pembayaran,
            'signer' => [
                'id' => $this->signer->id,
                'nip' => $this->signer->nip,
                'name' => $this->signer->name,
                'position' => $this->signer_position,
                'positions' => $this->signer->positions->pluck('position'),
            ],
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
