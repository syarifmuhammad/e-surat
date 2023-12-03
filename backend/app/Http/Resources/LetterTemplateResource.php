<?php

namespace App\Http\Resources;

use App\Models\ReferenceNumberSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LetterTemplateResource extends JsonResource
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
            'letter_type' => ReferenceNumberSetting::$DEFAULT[$this->letter_type]['name'],
            'name' => $this->name,
        ];
    }
}
