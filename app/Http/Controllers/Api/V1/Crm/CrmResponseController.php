<?php

namespace App\Http\Controllers\Api\V1\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\CrmResponseUpdateRequest;
use App\Http\Resources\Crm\CrmResponseResource;
use App\Models\Crm\Crm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CrmResponseController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $crm = Crm::with('responses')->findOrFail($id);
        $resource = CrmResponseResource::collection($crm->responses);
        $responseArray['data'] = $resource->resolve();
        $responseArray['crmName'] = $crm->name;
        return $responseArray;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CrmResponseUpdateRequest $request, string $id)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $crm = Crm::findOrFail($id);

            $this->removeNonExistentRecords(array_filter(array_column($data['fields'], 'id')), $crm->responses());

            foreach ($data['fields'] as $fieldData){
                if(!empty($fieldData['id'])){
                    $field = $crm->responses()->findOrFail($fieldData['id']);
                    $field->update($fieldData);
                }else{
                    $crm->responses()->create($fieldData);
                }
            }
            DB::commit();
            return response()->noContent();
        } catch (\Exception $e) {
            DB::rollBack();
            logs()->warning('CrmResponseController method update ' . $e->getMessage());
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
}
