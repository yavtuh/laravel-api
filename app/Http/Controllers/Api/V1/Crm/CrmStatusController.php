<?php

namespace App\Http\Controllers\Api\V1\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\CrmStatusUpdateRequest;
use App\Http\Resources\Crm\CrmStatusResource;
use App\Models\Crm\Crm;
use App\Models\Crm\CrmStatusField;
use App\Models\Crm\CrmStatusLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CrmStatusController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $crm = Crm::with('statusLead')->findOrFail($id);
        if($crm->statusLead){
            return  new CrmStatusResource($crm->statusLead);
        }

        return response()->json(['isRelation' => false, 'crmName' => $crm->name, 'leadFields' => Schema::getColumnListing('leads')]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CrmStatusUpdateRequest $request, string $id)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $crmStatus = CrmStatusLead::find($id);
            $updateArray = [
                'method' => $data['method'],
                'base_url' => $data['base_url'],
                'content_type' => $data['content_type'],
                'path_leads' => $data['path_leads'],
                'path_uuid' => $data['path_uuid'],
                'path_status' => $data['path_status'],
                'local_field' => $data['local_field'],
            ];
            if ($crmStatus) {
                $crmStatus->update($updateArray);
            } else {
                $updateArray['crm_id'] = $data['crmId'];
                $crmStatus = CrmStatusLead::create($updateArray);
            }

            $updatedIds = array_filter(array_column($data['fields'], 'id'));
            $this->removeNonExistentRecords($updatedIds, $crmStatus->fields());

            $this->updateFields($crmStatus, $data['fields']);

            DB::commit();
            return response()->noContent();
        } catch (\Exception $e) {
            DB::rollBack();
            logs()->warning('CrmStatusController method update ' . $e->getMessage());
            return response()->json(['msg' => "Oops, unexpected error, try again."], 500);
        }
    }

    private  function updateFields($crmStatus, $fields): void
    {
        collect($fields ?? [])->each(function ($fieldData) use ($crmStatus) {
            if(!empty($fieldData['id'])){
                $field = $crmStatus->fields()->findOrFail($fieldData['id']);
                $field->update($fieldData);
            }else{
                $crmStatus->fields()->create($fieldData);
            }
        });
    }
    private function removeNonExistentRecords($updatedIds, $relation): void
    {

        $modelClass = get_class($relation->getRelated());
        $existingIds = $relation->pluck('id')->toArray();
        $idsToDelete = array_diff($existingIds, $updatedIds);
        if (!empty($idsToDelete)) {
            $modelClass::destroy($idsToDelete);
        }
    }

}
