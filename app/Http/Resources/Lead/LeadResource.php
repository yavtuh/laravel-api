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
            'user' => $this->user ? $this->user->name : null,
            'crm' => $this->crm ? $this->crm->name : null,
            'funnel' => $this->funnel ? $this->funnel->name : null,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'utm' => $this->utm,
            'user_agent' => $this->user_agent,
            'ip' => $this->ip,
            'extra' => $this->extra,
            'domain' => $this->domain,
            'country' => $this->country,
            'sent_crms' => collect($this->sent_crms)->map(function ($crmId) use ($crms) {
                return $crms->get($crmId)?->name;
            }),
            'send_date' => $this->send_date
                ? Carbon::parse($this->send_date)->toDateTimeString()
                : null,
            'send_status' => $this->send_status,
            'lead_status' => $this->lead_status,
            'send_result' => $this->send_result,
            'response' => $this->response,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
