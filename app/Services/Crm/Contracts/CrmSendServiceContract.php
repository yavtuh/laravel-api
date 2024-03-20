<?php

namespace App\Services\Crm\Contracts;

use App\Models\Crm\Crm;
use App\Models\Crm\CrmCreateLead;
use App\Models\Crm\CrmHeader;
use App\Models\Crm\CrmResponse;
use App\Models\Funnel\Funnel;
use App\Models\Lead\Lead;
use Illuminate\Support\Collection;

interface CrmSendServiceContract
{
    public function dispatchLead(?Funnel $funnel, Lead $lead): void;
    public function send(Crm $crm, Lead $lead): bool;
    public function createFields(Collection $fields, Lead $lead): array;
    public function checkStatus(Collection $crmResponses, array $response): string;

}
