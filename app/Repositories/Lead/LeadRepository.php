<?php

namespace App\Repositories\Lead;

use App\Http\Requests\Lead\LeadStoreRequest;
use App\Models\Lead\Lead;
use App\Repositories\Lead\Contracts\LeadRepositoryContract;
use Illuminate\Support\Facades\DB;

class LeadRepository implements LeadRepositoryContract
{

    public function create(array $data): Lead|null
    {
        if (isset($data['phone']) && $data['phone'][0] != '+') {
            $data['phone'] = '+' . $data['phone'];
        }
        try {
            return Lead::create($data);
        }catch (\Exception $e){
            logs()->warning('LeadRepository method create ' . $e->getMessage());
            return null;
        }
    }

    public function update(array $data, Lead|int $id): bool
    {
        try{
            DB::beginTransaction();
            $lead = is_int($id) ? Lead::find($id) : $id;
            $lead->update($data);
            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollBack();
            logs()->warning('LeadRepository method update ' . $e->getMessage());
            return false;
        }
    }

    public function updateAllByCrm(array $data, int $crmId, string  $status = null): bool
    {
        try{
            DB::beginTransaction();
            Lead::where('crm_id', $crmId)
                ->when($status !== null, function ($query) use ($status) {
                    return $query->where('send_status', $status);
                })
                ->update($data);
            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollBack();
            logs()->warning('LeadRepository method updateAllByCrm ' . $e->getMessage());
            return false;
        }
    }

}
