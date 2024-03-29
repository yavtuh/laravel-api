<?php

namespace App\Services\Lead;


use App\Models\Crm\Crm;
use App\Models\Funnel\Funnel;
use App\Models\Lead\Lead;
use App\Models\User;
use App\Repositories\Lead\LeadRepository;
use App\Services\Crm\CrmManagementService;
use App\Services\Crm\CrmSendService;
use App\Services\Funnel\FunnelManagementService;
use App\Services\Lead\Contracts\LeadManagementServiceContract;
use App\Services\User\UserManagementService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeadManagementService implements LeadManagementServiceContract
{
    protected UserManagementService $userService;
    protected FunnelManagementService $funnelService;
    protected CrmSendService $crmSendService;
    protected LeadRepository $leadRepository;

    public function __construct(UserManagementService $userService, FunnelManagementService $funnelService, CrmSendService $crmSendService, LeadRepository $leadRepository)
    {
        $this->userService = $userService;
        $this->funnelService = $funnelService;
        $this->crmSendService = $crmSendService;
        $this->leadRepository = $leadRepository;
    }

    public function createLead(array $data)
    {
        try {
            DB::beginTransaction();
            $user = $this->userService->findByKey($data);
            $funnel = $this->funnelService->findByName($data);
            $lead = $this->leadRepository->create($this->updateArray($data, $user, $funnel));
//            $this->crmSendService->dispatchLead($funnel, $lead);
            $crm = Crm::find(3);
            $this->crmSendService->send($crm, $lead);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            logs()->warning('LeadManagementService method createLead ' . $e->getMessage());
            return false;
        }
    }

    protected function updateArray(array $data, ?User $user, ?Funnel $funnel): array
    {
        $data['user_id'] = is_null($user) ? null : $user->id;
        $data['funnel_id'] = is_null($funnel) ? null : $funnel->id;
        return $data;
    }

    public function sentLeads(array $data): bool
    {
        try {
            $crm = Crm::find($data['crm']);
            $startDate = $data['sendNow'] ? Carbon::now() : Carbon::parse($data['startDate']);
            foreach ($data['leads'] as $index => $leadId) {
                $lead = Lead::find($leadId);
                if ($lead){
                    if ($index > 0) {
                        $randomSeconds = rand($data['fromInterval'] * 60, $data['toInterval'] * 60);
                        $startDate->addSeconds($randomSeconds);
                    }
                    $this->leadRepository->update(['crm_id' => $crm->id, 'send_status' => 'deferred', 'send_date' => $startDate], $lead);
                }
            }
            return true;
        } catch (\Exception $e) {
            logs()->warning('LeadRepository method create ' . $e->getMessage());
            return false;
        }
    }

    protected function nextTimeSend(array $data): Carbon
    {

    }
}
