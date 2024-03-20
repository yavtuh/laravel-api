<?php

namespace App\Http\Resources\Funnel;

use App\Models\Crm\Crm;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FunnelResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'settings' => FunnelSettingResource::collection($this->settings)
        ];
    }
}
