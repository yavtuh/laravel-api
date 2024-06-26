<?php

namespace App\Http\Resources\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CrmHeaderAuthResource extends JsonResource
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
            'base_url' => $this->base_url,
            'method' => $this->method,
            'token_path' => $this->token_path,
            'auth_type' => $this->auth_type,
            'content_type' => $this->content_type,
            'fields' => CrmHeaderAuthFieldResource::collection($this->fields)
        ];
    }
}
