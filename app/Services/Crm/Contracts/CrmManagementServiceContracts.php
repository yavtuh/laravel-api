<?php

namespace App\Services\Crm\Contracts;

use App\Models\Crm\Crm;
use App\Models\Funnel\Funnel;
use App\Models\Funnel\FunnelSetting;
use Illuminate\Database\Eloquent\Collection;

interface CrmManagementServiceContracts
{
    public function findMatchCrm(Funnel $funnel, array $excludedCrms = []): array|null;
    public function getCrmByFunnel(?Funnel $funnel, array $excludedCrms = []): Crm|null;
    public function countLeadTodayByFunnel(array $crms, int $funnelId): array;
    public function isSendCrm(Crm|int $crm): array;
}
