<?php

namespace App\Http\Resources\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CrmResponseResource extends JsonResource
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
            'response_type' => $this->response_type,
            'response_path' => $this->response_path,
            'expected_value' => $this->expected_value,
            'is_empty' => $this->is_empty,
            'expected_type' => $this->expected_type,
        ];
    }
}
