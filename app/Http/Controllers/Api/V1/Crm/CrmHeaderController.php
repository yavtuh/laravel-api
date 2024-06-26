<?php

namespace App\Http\Controllers\Api\V1\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\CrmHeaderUpdateRequest;
use App\Http\Resources\Crm\CrmHeaderResource;
use App\Models\Crm\Crm;
use App\Models\Crm\CrmHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CrmHeaderController extends Controller
{

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new CrmHeaderResource(Crm::with('headers')->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CrmHeaderUpdateRequest $request, string $id)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $crm = Crm::findOrFail($id);
            $headers = $crm->headers();
            $this->removeNonExistentRecords(array_filter(array_column($data['headers'], 'id')), $headers);

            foreach ($data['headers'] as $headerData){
                !empty($headerData['id']) ? $this->updateHeader($headerData) : $this->createHeader($crm, $headerData);
            }

            DB::commit();
            return response()->noContent();
        } catch (\Exception $e) {
            DB::rollBack();
            logs()->warning('CrmHeaderController method update ' . $e->getMessage());
            return response()->json(['msg' => "Oops, unexpected error, try again."], 500);
        }
    }

    private function removeNonExistentRecords($updatedIds, $relation): void
    {
        $modelClass = get_class($relation->getRelated());
        $existingIds = $relation->pluck('id')->toArray();
        $idsToDelete = array_diff($existingIds, $updatedIds);
        $modelClass::destroy($idsToDelete);
    }

    private function updateHeader($headerData): void
    {
        $header = CrmHeader::findOrFail($headerData['id']);
        if(is_array($headerData['header_value'])){
            $header->update([
                'header_name' => $headerData['header_name'],
            ]);
            $auth = $header->auth;
            $auth->update([
                'auth_type' => $headerData['header_value']['auth_type'],
                'base_url' => $headerData['header_value']['base_url'],
                'method' => $headerData['header_value']['method'],
                'token_path' => $headerData['header_value']['token_path']
            ]);
            $this->removeNonExistentRecords(array_filter(array_column($headerData['header_value']['fields'], 'id')), $auth->fields());
            foreach ($headerData['header_value']['fields'] as $field){
                if(!empty($field['id'])){
                    $auth->fields()->findOrFail($field['id'])->update($field);
                }else{
                    $auth->fields()->create($field);
                }
            }
        }else{
            $header->update([
                'header_name' => $headerData['header_name'],
                'header_value' => $headerData['header_value']
            ]);
        }
    }

    private function createHeader($crm, $headerData): void
    {
        $crmHeader = $crm->headers()->create([
            'header_name' => $headerData['header_name'],
            'header_value' => is_array($headerData['header_value']) ? null : $headerData['header_value']
        ]);
        if(is_array($headerData['header_value'])){
            $crmHeaderAuth = $crmHeader->auth()->create([
                'auth_type' => $headerData['header_value']['auth_type'],
                'base_url' => $headerData['header_value']['base_url'],
                'method' => $headerData['header_value']['method'],
                'token_path' => $headerData['header_value']['token_path'],
            ]);
            foreach ($headerData['header_value']['fields'] as $field){
                $crmHeaderAuth->fields()->create($field);
            }
        }
    }
}
