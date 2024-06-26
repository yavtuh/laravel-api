<?php

namespace App\Services\Crm;


use App\Models\Crm\Crm;
use App\Models\Funnel\Funnel;
use App\Models\Lead\Lead;
use App\Services\Crm\Contracts\CrmManagementServiceContracts;
use Carbon\Carbon;
use \Illuminate\Database\Eloquent\Collection;

class CrmManagementService implements CrmManagementServiceContracts
{

    public function findMatchCrm(Funnel $funnel, array $excludedCrms = []): array|null
    {

        $selectedCrm = $this->getCrmByFunnel($funnel, $excludedCrms);

        if(is_null($selectedCrm)){
            return null;
        }

        $sendInfo = $this->isSendCrm($selectedCrm);

        if ($selectedCrm && !$sendInfo['send_now']) {
            return $sendInfo['skip'] ? $this->findMatchCrm($funnel, array_merge($excludedCrms, [$selectedCrm->id])) : ['crm' => $selectedCrm, 'send_now' => $sendInfo['send_now']];
        }

        return ['crm' => $selectedCrm, 'send_now' => $sendInfo['send_now']];
    }

    public function isSendCrm(Crm|int $crm): array
    {
        $settings = is_int($crm) ? Crm::find($crm)->settings : $crm->settings;

        if ($settings->isWorkingTime()) {
            return ['send_now' => true, 'skip' => false];
        }

        return ['send_now' => false, 'skip' => $settings->skip_after_workings];
    }

    public function getCrmByFunnel(?Funnel $funnel, array $excludedCrms = []): Crm|null
    {
        if(is_null($funnel)){
            return null;
        }

        $funnelSettings = $funnel->settings()->whereHas('crm', function($query){
            $query->whereHas('settings', function($q) {
                $q->where('is_active', true);
            });
        })->whereNotIn('crm_id', $excludedCrms)->get();

        $filteredSettings = $funnelSettings->filter(function ($setting) {
            return !$setting->crm->isFilledCapToday();
        });

        $totalScore = $filteredSettings->sum('score');

        if ($filteredSettings->isEmpty() || $totalScore === 0) {
            return null;
        }

        $leadsCount = $this->countLeadTodayByFunnel($filteredSettings->pluck('crm_id')->toArray(), $funnel->id);
        $crmId = $this->determineCrmForNextLead($filteredSettings, $leadsCount, $totalScore);

        return Crm::find($crmId);
    }

    public function countLeadTodayByFunnel(array $crms, int $funnelId): array
    {
        //завтра обратить внимание очень сильно
        $leadsCount = [];

        foreach ($crms as $crmId) {
            $leadsCount[$crmId] = Lead::where('funnel_id', $funnelId)->where('crm_id', $crmId)->whereDate('created_at', Carbon::today())->count();
        }

        return $leadsCount;
    }

    protected function determineCrmForNextLead(Collection $funnelSettings, array $leadsCount, int $totalScore): int
    {
        $totalLeadsSent = array_sum($leadsCount);

        $normalizedScores = $funnelSettings->mapWithKeys(function ($setting) use ($totalScore) {
            return [$setting->crm_id => $setting->score / $totalScore * 100];
        });

        $crmDeficits = $normalizedScores->mapWithKeys(function ($normalizedScore, $crmId) use ($leadsCount, $totalLeadsSent) {
            $actualPercentage = ($totalLeadsSent > 0) ? ($leadsCount[$crmId] / $totalLeadsSent * 100) : 0;
            return [$crmId => $normalizedScore - $actualPercentage];
        });

        return array_keys($crmDeficits->toArray(), max($crmDeficits->toArray()))[0];
    }


}
