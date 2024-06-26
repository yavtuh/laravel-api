<?php

namespace App\Http\Resources\Lead;

use App\Models\Crm\Crm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $crms = Crm::findMany($this->sent_crms)->keyBy('id');
        return [
            'id' => $this->id,
            'user' => $this->user ? $this->user->name : 'Нету',
            'crm' => $this->crm ? $this->crm->name : 'Нету',
            'funnel' => $this->funnel ? $this->funnel->name : 'Нету',
            'first_name' => $this->first_name ?? 'Нету',
            'last_name' => $this->last_name ?? 'Нету',
            'email' => $this->email ?? 'Нету',
            'phone' => $this->phone,
            'utm' => $this->utm ?? 'Нету',
            'user_agent' => $this->user_agent ?? 'Нету',
            'ip' => $this->ip ?? 'Нету',
            'extra' => $this->extra ?? 'Нету',
            'domain' => $this->domain ?? 'Нету',
            'country' => $this->country ?? 'Нету',
            'sent_crms' => collect($this->sent_crms)->map(function ($crmId) use ($crms) {
                return $crms->get($crmId)?->name;
            }),
            'send_date' => $this->send_date
                ? Carbon::parse($this->send_date)->toDateTimeString()
                : 'Нету',
            'send_status' => $this->send_status ?? 'Неопределённый',
            'lead_status' => $this->lead_status ?? 'Неопределённый',
            'send_result' => $this->send_result ?? 'Неопределённый',
            'response' => $this->response,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
