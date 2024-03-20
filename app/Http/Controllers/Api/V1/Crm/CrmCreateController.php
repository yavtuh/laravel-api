<?php

namespace App\Http\Controllers\Api\V1\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\CrmCreateUpdateRequest;
use App\Http\Resources\Crm\CrmCreateResource;
use App\Models\Crm\Crm;
use App\Models\Crm\CrmCreateField;
use App\Models\Crm\CrmCreateLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CrmCreateController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $crm = Crm::with('createLead')->findOrFail($id);
        if($crm->createLead){
            return new CrmCreateResource($crm->createLead);
        }

        return response()->json(['isRelation' => false, 'crmName' => $crm->name, 'leadFields' => Schema::getColumnListing('leads')]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CrmCreateUpdateRequest $request, string $id)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $crmCreate = CrmCreateLead::find($id);
            $updateArray = [
                'method' => $data['method'],
                'base_url' => $data['base_url'],
                'content_type' => $data['content_type'],
                'uuid_path' => $data['uuid_path']
            ];
            if ($crmCreate) {
                $crmCreate->update($updateArray);
            } else {
                $updateArray['crm_id'] = $data['crmId'];
                $crmCreate = CrmCreateLead::create($updateArray);
            }
            $updatedIds = array_filter(array_column($data['fields'], 'id'));
            $this->removeNonExistentRecords($updatedIds, $crmCreate->fields());

            $this->updateFields($crmCreate, $data['fields']);

            DB::commit();
            return response()->noContent();
        } catch (\Exception $e) {
            DB::rollBack();
            logs()->warning('CrmCreateController method update ' . $e->getMessage());
            return response()->json(['msg' => "Oops, unexpected error, try again."], 500);
        }
    }

    private  function updateFields($crmCreate, $fields): void
    {
        collect($fields ?? [])->each(function ($fieldData) use ($crmCreate) {
            if(!empty($fieldData['id'])){
                $field = $crmCreate->fields()->findOrFail($fieldData['id']);
                $field->update($fieldData);
            }else{
                $crmCreate->fields()->create($fieldData);
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
