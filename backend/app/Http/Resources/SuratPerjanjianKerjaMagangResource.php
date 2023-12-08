<?php

namespace App\Http\Resources;

use App\Models\ReferenceNumberSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\SuratPerjanjianKerjaMagang as Letter;

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
            'reference_number' => $reference_number,
            'employee' => [
                'id' => $this->employee->id,
                'nik' => $this->employee->nik,
                'nip' => $this->employee->nip,
                'name' => $this->employee->name,
                'position' => $this->position,
            ],
            'signer' => [
                'id' => $this->signer->id,
                'nip' => $this->signer->nip,
                'name' => $this->signer->name,
                'position' => $this->signer_position,
            ],
            'have_reference_number' => $this->have_reference_number(),
            'can_give_reference_number' => $this->can_give_reference_number(),
            'can_signed' => $this->can_signed(),
            'can_edit' => $this->can_edit(),
            'can_upload_verified_file' => $this->can_upload_verified_file(),
            'signature_type' => $this->signature_type,
            'is_signed' => $this->is_signed(),
            'status' => $status,
            'created_at' => $this->created_at,
        ];
    }
}
