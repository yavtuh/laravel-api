<?php

namespace App\Services\Crm\Contracts;

use App\Models\Crm\Crm;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface CrmStatusServiceContract
{
    public function send(Crm $crm, Carbon $startDate, Carbon $endDate): bool;
    public function statusFields(Collection $fields, Carbon $startDate, Carbon $endDate): array;

}
