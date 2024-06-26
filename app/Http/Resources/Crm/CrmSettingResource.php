<?php

namespace App\Http\Resources\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CrmSettingResource extends JsonResource
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
            'working_hours_start' => $this->working_hours_start->format('H:i'),
            'working_hours_end' => $this->working_hours_end->format('H:i'),
            'working_days' => $this->working_days,
            'daily_cap' => $this->daily_cap,
            'is_active' => $this->is_active,
            'generate_email_if_missing' => $this->generate_email_if_missing,
            'skip_after_workings' => $this->skip_after_workings,
            'created_at' => $this->created_at->toDateTimeString(),
            'crmName' => $this->crm->name,
            'isRelation' => true,
        ];
    }
}
