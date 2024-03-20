<?php

namespace App\Services\Crm\Contracts;

use App\Models\Crm\CrmHeader;
use Illuminate\Support\Collection;

interface CrmHeaderServiceContract
{
    public function headers(Collection $headers): array;
    public function headerAuthToken(CrmHeader $header): string;
}
