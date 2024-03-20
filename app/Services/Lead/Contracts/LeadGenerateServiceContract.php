<?php

namespace App\Services\Lead\Contracts;

use App\Models\Lead\Lead;

interface LeadGenerateServiceContract
{
    public function generateEmptyValue(string $field, Lead $lead);
}
