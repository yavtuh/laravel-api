<?php

namespace App\Http\Controllers\Api\V1\Funnel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Funnel\FunnelStoreRequest;
use App\Http\Requests\Funnel\FunnelUpdateRequest;
use App\Http\Resources\Funnel\FunnelResource;
use App\Models\Crm\Crm;
use App\Models\Funnel\Funnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FunnelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resource =  FunnelResource::collection(Funnel::with('settings')->get());
        $responseArray['crms'] = Crm::all()->map(function ($crm) {
            return ['id' => $crm->id, 'name' => $crm->name];
        })->values()->toArray();
        $responseArray['data'] = $resource->resolve();
        return $responseArray;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FunnelStoreRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $funnel = Funnel::create([
                'name' => $data['name'],
                'description' => $data['description']
            ]);
            foreach ($data['settings'] as $setting){
                if($setting['score'] > 0){
                    $funnel->settings()->create([
                        'crm_id' => (int)$setting['crm_id'],
                        'score' => (int)$setting['score']
                    ]);
                }
            }

            DB::commit();
            return response()->noContent();
        }catch (\Exception $e){
            DB::rollBack();
            logs()->warning('FunnelController method store '.$e->getMessage());
            return response()->json(['msg' => "Oops, unexpected error, try again."], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FunnelUpdateRequest $request, string $id)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $funnel = Funnel::findOrFail($id);
            $funnel->update(['name' => $data['name'], 'description' => $data['description']]);
            $this->removeNonExistentRecords(array_filter(array_column($data['settings'], 'id')), $funnel->settings());
            foreach ($data['settings'] as $setting) {
                if(!empty($setting['id'])){
                    $funnel->settings()->findOrFail($setting['id'])->update(['score' => (int)$setting['score']]);
                }else{
                    $funnel->settings()->create(['crm_id' => $setting['crm_id'], 'score' => $setting['score']]);
                }
            }
            DB::commit();
            return $this->index();
        }catch (\Exception $e){
            DB::rollBack();
            logs()->warning('FunnelController method update '.$e->getMessage());
            return response()->json(['msg' => "Oops, unexpected error, try again."], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            Funnel::findOrFail($id)->delete();
            DB::commit();
            return response()->noContent();
        }catch (\Exception $e){
            DB::rollBack();
            logs()->warning('FunnelController method destroy '.$e->getMessage());
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
