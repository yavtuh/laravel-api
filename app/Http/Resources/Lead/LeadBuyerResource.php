<?php

namespace App\Http\Resources\Lead;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadBuyerResource extends JsonResource
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
            'first_name' => $this->first_name ?? 'Нету',
            'last_name' => $this->last_name ?? 'Нету',
            'email' => $this->email ?? 'Нету',
            'utm' => $this->utm ?? 'Нету',
            'domain' => $this->domain ?? 'Нету',
            'send_date' => $this->send_date
                ? Carbon::parse($this->send_date)->toDateTimeString()
                : 'Нету',
            'send_status' => $this->send_status ?? 'Неопределённый',
            'lead_status' => $this->lead_status ?? 'Неопределённый',
            'send_result' => $this->send_result ?? 'Неопределённый',
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
