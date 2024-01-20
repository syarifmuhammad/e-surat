<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApprovalResource extends JsonResource
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
            'employee' => [
                'id' => $this->employee->id,
                'name' => $this->employee->name,
            ],
            'employee_id' => $this->employee_id,
            'position' => $this->position,
            'is_signer' => $this->is_signer,
            'signed_file' => $this->signed_file,
            'is_approved' => $this->is_approved,
        ];
    }
}
