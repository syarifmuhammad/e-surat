<?php

namespace App\Http\Resources;

use App\Models\ReferenceNumberSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\SuratKeteranganKerja as Letter;
use Carbon\Carbon;

class SuratKeteranganKerjaResource extends JsonResource
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
        } else if(!$this->is_approved()) {
            $status = 'waiting_for_approval';
        } else if (!$this->is_signed()) {
            $status = 'waiting_for_signed';
        } else if ($this->is_signed()) {
            $status = 'signed';
        }
        return [
            'id' => $this->id,
            'is_private' => $this->is_private,
            'letter_template_id' => $this->letter_template_id,
            'reference_number' => $reference_number,
            'employee' => [
                'id' => $this->employee->id,
                'nip' => $this->employee->nip,
                'name' => $this->employee->name,
                'position' => $this->position,
                'positions' => $this->employee->positions->pluck('position'),
            ],
            'signers' => ApprovalResource::collection($this->signers),
            'approvals' => ApprovalResource::collection($this->approvals),
            'current_approval' => $this->approvals->where('is_approved', false)->first(),
            'have_reference_number' => $this->have_reference_number(),
            'can_give_reference_number' => $this->can_give_reference_number(),
            'can_approved' => $this->can_approved(),
            'can_signed' => $this->can_signed(),
            'can_edit' => $this->can_edit(),
            'can_upload_verified_file' => $this->can_upload_verified_file(),
            'signature_type' => $this->signature_type,
            'is_signed' => $this->is_signed(),
            'status' => $status,
            'tanggal_surat_raw' => $this->tanggal_surat,
            'tanggal_surat' => Carbon::parse($this->tanggal_surat)->translatedFormat('l, d F Y'),
            'masa_berlaku' => $this->masa_berlaku_parse(),
        ];
    }
}
