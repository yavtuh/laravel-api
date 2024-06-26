<?php

namespace App\Services\Lead\Contracts;

interface LeadManagementServiceContract
{
    public function createLead(array $data );

    public function sentLeads(array $data): bool;

}
