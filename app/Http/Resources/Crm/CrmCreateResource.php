<?php

namespace App\Http\Resources\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Schema;

class CrmCreateResource extends JsonResource
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
            'content_type' => $this->content_type,
            'uuid_path' => $this->uuid_path,
            'fields' => CrmCreateLeadsResource::collection($this->fields),
            'leadFields' => Schema::getColumnListing('leads'),
            'crmName' => $this->crm->name,
            'isRelation' => true,
        ];
    }
}
