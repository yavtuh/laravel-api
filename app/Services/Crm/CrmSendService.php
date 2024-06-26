<?php

namespace App\Services\Crm;

use App\Jobs\SendLeadToCrmJob;
use App\Models\Crm\Crm;
use App\Models\Funnel\Funnel;
use App\Models\Lead\Lead;
use App\Repositories\Lead\LeadRepository;
use App\Services\Lead\LeadGenerateService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class CrmSendService implements Contracts\CrmSendServiceContract
{
    protected CrmManagementService $crmService;
    protected LeadRepository $leadRepository;
    protected LeadGenerateService $leadFilterService;

    protected CrmHeaderService $crmHeaderService;

    public function __construct(CrmManagementService $crmService, LeadRepository $leadRepository, LeadGenerateService $leadFilterService, CrmHeaderService $crmHeaderService)
    {
        $this->crmService = $crmService;
        $this->leadRepository = $leadRepository;
        $this->leadFilterService = $leadFilterService;
        $this->crmHeaderService = $crmHeaderService;
    }

    public function dispatchLead(?Funnel $funnel, Lead $lead): void
    {
        $crmData = $this->crmService->findMatchCrm($funnel, $lead->sent_crms ?? []);
        if (!is_null($crmData)) {
            $crm = $crmData['crm'];
            if ($crmData['send_now']) {
                $updateArray = ['crm_id' => $crm->id, 'send_status' => 'processing', 'send_date' => Carbon::now()];
                $this->leadRepository->update($updateArray, $lead);
                SendLeadToCrmJob::dispatch($lead->id);
            } else {
                $updateArray = ['crm_id' => $crm->id, 'send_status' => 'deferred', 'send_date' => $crm->nextSendTime()];
                $this->leadRepository->update($updateArray, $lead);
            }
        } else {
            $updateArray = ['crm_id' => null, 'send_date' => null, 'lead_status' => null, 'send_status' => null, 'send_result' => null];
            $this->leadRepository->update($updateArray, $lead);
        }
    }

    public function send(Crm $crm, Lead $lead): bool
    {
        if (!$crm->isActive()) {
            $updateArray = ['crm_id' => null, 'send_date' => null, 'lead_status' => null, 'send_status' => null, 'send_result' => null];
            $this->leadRepository->updateAllByCrm($updateArray, $crm->id, 'deferred');
            return false;
        }

        if ($crm->isFilledCapToday()) {
            $updateArray = ['send_status' => 'deferred', 'send_date' => $crm->nextSendTime()];
            $this->leadRepository->update($updateArray, $lead);
            return true;
        }

        $response = $this->sendRequestToCrm($crm, $lead);
        $this->handleCrmResponse($response, $crm, $lead, $crm->settings->skip_after_workings);
        return true;
    }


    private function sendRequestToCrm(Crm $crm, Lead $lead)
    {
        $createLead = $crm->createLead;
        $headers = $this->crmHeaderService->headers($crm->headers);
        $data = $this->createFields($createLead->fields, $lead);
        dd($data, $lead);
        $client = new Client();

        return $client->request($createLead['method'], $createLead['base_url'], [
            'verify' => env('VERIFY_SSL', true),
            'headers' => $headers,
            $createLead['content_type'] => $data
        ]);
    }

    private function handleCrmResponse($response, Crm $crm, Lead $lead, bool $skipOnFailure): void
    {
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $content = json_decode($response->getBody()->getContents(), true);
            $status = $this->checkStatus($crm->responses, $content ?? []);
            $this->updateLeadStatus($lead, $crm, $status, $content);

            if ($status !== 'success' && $skipOnFailure && $lead->funnel_id) {
                $this->dispatchLead(Funnel::find($lead->funnel_id) ?? null, $lead);
            }

        } elseif ($skipOnFailure && $lead->funnel_id) {
            $this->dispatchLead(Funnel::find($lead->funnel_id) ?? null, $lead);
        }
    }

    private function updateLeadStatus(Lead $lead, Crm $crm, $status, $content): void
    {
        $sentCrms = $lead->sent_crms ?? [];
        if (!in_array($crm->id, $sentCrms)) {
            $sentCrms[] = $crm->id;
        }

        $uuid = get_value_by_path($content, $crm->createLead->uuid_path);

        $existingResponses = json_decode($lead->response, true) ?? [];

        $updatedResponses = $existingResponses;
        $updatedResponses[] = [
            'crm_name' => $crm->name,
            'response' => $content
        ];

        $updatedContent = json_encode($updatedResponses);
        $updateData = ['sent_crms' => $sentCrms, 'send_result' => $status, 'send_status' => 'sent', 'response' => $updatedContent, 'uuid' => $uuid];
        if ($status !== 'success') {
            $updateData['crm_id'] = null;
        }

        $this->leadRepository->update($updateData, $lead);
    }

    public function createFields(Collection $fields, Lead $lead): array
    {
        return $fields->mapWithKeys(function ($field) use ($lead) {
            if($field->is_random){
                if($field['field_type'] === 'string'){
                    $rand = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(15/strlen($x)) )),1,10);
                    $this->leadRepository->update(['uuid' => $rand], $lead);
                    return [$field['remote_field'] => cast_value($rand, $field['field_type'])];
                }
                $randomNumber = '';
                $randomNumber .= rand(1, 9);
                for ($i = 0; $i < 14; $i++) {
                    $randomNumber .= rand(0, 9);
                }
                $this->leadRepository->update(['uuid' => $randomNumber], $lead);
                return [$field['remote_field'] => cast_value($randomNumber, $field['field_type'])];
            }

            if (is_null($field['local_field'])) {
                return [$field['remote_field'] => cast_value($field['another_value'], $field['field_type'])];
            }

            if ($field->is_required && is_null($lead->{$field['local_field']} ?? null)) {
                $generateField = $this->leadFilterService->generateEmptyValue($field['local_field'], $lead);
                return [$field['remote_field'] => cast_value($generateField, $field['field_type'])];
            }

            return [$field['remote_field'] => cast_value($lead->{$field['local_field']}, $field['field_type'])];
        })->toArray();
    }

    public function checkStatus(Collection $crmResponses, array $response): string
    {
        $statuses = [];

        foreach ($crmResponses as $crmResponse) {
            $expectedValue = get_value_by_path($response, $crmResponse['response_path']);

            if (!is_null($expectedValue) && check_value(cast_value($crmResponse['expected_value'], $crmResponse['expected_type']), $expectedValue)) {
                $statuses[] = $crmResponse['response_type'];
            } elseif ($crmResponse->is_empty && empty($response)) {
                $statuses[] = $crmResponse['response_type'];
            }

        }

        if (in_array('duplicate', $statuses)) {
            return 'duplicate';
        }

        return $statuses[0] ?? 'error';
    }
}
