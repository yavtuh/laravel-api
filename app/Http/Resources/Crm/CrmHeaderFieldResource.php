<?php

namespace App\Http\Resources\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CrmHeaderFieldResource extends JsonResource
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
            'header_name' => $this->header_name,
            'header_value' => is_null($this->header_value) ? new CrmHeaderAuthResource($this->auth) : $this->header_value,

        ];
    }
}
