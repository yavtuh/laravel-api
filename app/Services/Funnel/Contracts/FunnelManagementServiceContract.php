<?php

namespace App\Services\Funnel\Contracts;

use App\Models\Funnel\Funnel;

interface FunnelManagementServiceContract
{
    public function findByName(array $data): Funnel|null;
}
