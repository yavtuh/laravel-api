<?php

namespace App\Http\Controllers\Api\V1\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\CrmSettingsUpdateRequest;
use App\Http\Resources\Crm\CrmSettingResource;
use App\Models\Crm\Crm;
use App\Models\Crm\CrmSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CrmSettingsController extends Controller
{

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $crm = Crm::with('settings')->findOrFail($id);
        if($crm->settings){
            return new CrmSettingResource($crm->settings);
        }

        return response()->json(['isRelation' => false, 'crmName' => $crm->name]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CrmSettingsUpdateRequest $request, string $id)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $crmSettings = CrmSetting::find($id);
            $updateArray = [
              'working_days' => $data['working_days'],
              'working_hours_start' => $data['working_hours_start'],
              'working_hours_end' => $data['working_hours_end'],
              'daily_cap' => $data['daily_cap'],
              'is_active' => $data['is_active'],
              'skip_after_workings' => $data['skip_after_workings'],
              'generate_email_if_missing' => $data['generate_email_if_missing'],
            ];
            if ($crmSettings) {
                $crmSettings->update($updateArray);
            } else {
                $updateArray['crm_id'] = $data['crmId'];
                CrmSetting::create($updateArray);
            }
            DB::commit();
            return response()->noContent();
        } catch (\Exception $e) {
            DB::rollBack();
            logs()->warning('CrmSettingsController method update ' . $e->getMessage());
            return response()->json(['msg' => "Oops, unexpected error, try again."], 500);
        }
    }

}
