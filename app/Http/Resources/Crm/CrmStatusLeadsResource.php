<?php

namespace App\Http\Resources\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CrmStatusLeadsResource extends JsonResource
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
            'remote_field' => $this->remote_field,
            'field_type' => $this->field_type,
            'another_value' => $this->another_value,
            'is_start_date' => $this->is_start_date,
            'is_end_date' => $this->is_end_date,
        ];
    }
}
