<?php

namespace App\Services\Crm;

use App\Models\Crm\Crm;
use App\Models\Lead\Lead;
use App\Repositories\Lead\LeadRepository;
use App\Services\Crm\Contracts\CrmStatusServiceContract;
use App\Services\Lead\LeadGenerateService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use GuzzleHttp\Client;

class CrmStatusService implements Contracts\CrmStatusServiceContract
{

    protected LeadRepository $leadRepository;

    protected CrmHeaderService $crmHeaderService;

    public function __construct(LeadRepository $leadRepository, CrmHeaderService $crmHeaderService)
    {
        $this->leadRepository = $leadRepository;
        $this->crmHeaderService = $crmHeaderService;
    }

    public function send(Crm $crm, Carbon $startDate, Carbon $endDate): bool
    {
        try {
            $response = $this->sendRequestToCrm($crm, $startDate, $endDate);
            $this->handleCrmResponse($response, $crm);
            return true;
        }catch (\Exception $e){
            logs()->warning('CrmStatusService method send ' . $e->getMessage());
            return false;
        }
    }

    public function statusFields(Collection $fields, Carbon $startDate, Carbon $endDate): array
    {
        return $fields->mapWithKeys(function ($field) use($startDate, $endDate) {
            if($field->is_start_date){
                return [$field['remote_field'] => $startDate];
            }

            if($field->is_end_date){
                return [$field['remote_field'] => $endDate];
            }

            return [$field['remote_field'] => cast_value($field['another_value'], $field['field_type'])];
        })->toArray();
    }

    private function sendRequestToCrm(Collection $crm, Carbon $startDate, Carbon $endDate)
    {
        $statusLead = $crm->statusLead;
        $headers = $this->crmHeaderService->headers($crm->headers);
        $data = $this->statusFields($statusLead->fields, $startDate, $endDate);
        $client = new Client();

        return $client->request($statusLead['method'], $statusLead['base_url'], [
            'verify' => env('VERIFY_SSL', true),
            'headers' => $headers,
            $statusLead['content_type'] => $data
        ]);
    }

    private function handleCrmResponse($response, Crm $crm): void
    {
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $content = json_decode($response->getBody()->getContents(), true);
            $statusLead = $crm->statusLead;
            $leadsArray =  $statusLead['path_leads'] ? get_value_by_path($content, $statusLead['path_leads']) : $content;
            foreach ($leadsArray as $leadArray){
                $uuid = get_value_by_path($leadArray, $statusLead['path_uuid']);
                $status = get_value_by_path($leadArray, $statusLead['path_status']);
                $lead = Lead::where($statusLead['local_field'], $uuid)->first();
                if ($lead) {
                    $this->leadRepository->update(['lead_status' => $status], $lead);
                }
            }
        }
    }
}
