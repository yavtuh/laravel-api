<?php

namespace App\Jobs;

use App\Models\Lead\Lead;
use App\Services\Crm\CrmSendService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendLeadToCrmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $leadId;

    /**
     * Create a new job instance.
     */
    public function __construct($leadId)
    {
        $this->leadId = $leadId;
    }

    /**
     * Execute the job.
     */
    public function handle(CrmSendService $crmSendService): void
    {
        $lead = Lead::with('crm')->has('crm')->find($this->leadId);
        if($lead){
            $crm = $lead->crm;
            $crmSendService->send($crm, $lead);
        }
    }
}
