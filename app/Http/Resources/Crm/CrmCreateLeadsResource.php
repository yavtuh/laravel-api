<?php

namespace App\Http\Resources\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CrmCreateLeadsResource extends JsonResource
{
    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'local_field' => $this->local_field,
            'remote_field' => $this->remote_field,
            'field_type' => $this->field_type,
            'another_value' => $this->another_value,
            'is_required' => $this->is_required,
            'is_random' => $this->is_random,
        ];
    }
}
