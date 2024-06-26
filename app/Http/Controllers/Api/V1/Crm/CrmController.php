<?php

namespace App\Http\Controllers\Api\V1\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\CrmStoreRequest;
use App\Http\Resources\Crm\CrmNameResource;
use App\Http\Resources\Crm\CrmResource;
use App\Models\Crm\Crm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CrmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return CrmResource::collection(Crm::with('settings')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CrmStoreRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            Crm::create($data);
            DB::commit();
            return response()->noContent();
        }catch (\Exception $e){
            DB::rollBack();
            logs()->warning('CrmController method store '.$e->getMessage());
            return response()->json(['msg' => "Oops, unexpected error, try again."], 500);
        }
    }

//    public function store(CrmStoreRequest $request)
//    {
//        $data = $request->validated();
//
//        try {
//            DB::beginTransaction();
//            $crm = Crm::create($data['crm']);
//            $createLead = isset($data['statusLeads']) ? $crm->createLead()->create($data['createLeads']) : '';
//            collect($data['fields'] ?? [])->each(function ($field) use ($createLead) {
//                $createLead->fields()->create($field);
//            });
//            collect($data['headers'] ?? [])->each(function ($header) use ($crm, $data) {
//                $crmHeader = $crm->headers()->create($header);
//                if($header['header_value'] === null || $header['header_value'] === ''){
//                    $crmHeaderAuth = $crmHeader->auth()->create($data['headerAuth']);
//                    collect($data['headerAuthFields'] ?? [])->each(function ($field) use ($crmHeaderAuth) {
//                        $crmHeaderAuth->fields()->create($field);
//                    });
//                }
//            });
//            $statusLead = isset($data['statusLeads']) ? $crm->statusLead()->create($data['statusLeads']) : '';
//            collect($data['fieldsStatusLeads'] ?? [])->each(function ($field) use ($statusLead) {
//                $statusLead->fields()->create($field);
//            });
//
//            isset($data['settings']) ? $crm->settings()->create($data['settings']) : '';
//
//            collect($data['responseLeads'] ?? [])->each(function ($field) use ($crm) {
//                $crm->responses()->create($field);
//            });
//            DB::commit();
//            return response()->noContent();
//        }catch (\Exception $e){
//            DB::rollBack();
//            logs()->warning('CrmController method store '.$e->getMessage());
//            return response()->json(['msg' => "Oops, unexpected error, try again."], 500);
//        }
//    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            Crm::findOrFail($id)->delete();
            DB::commit();
            return response()->noContent();
        }catch (\Exception $e){
            DB::rollBack();
            logs()->warning('CrmController method destroy '.$e->getMessage());
            return response()->json(['msg' => "Oops, unexpected error, try again."], 500);
        }
    }

    public function getLeadFields()
    {
        return response()->json(Schema::getColumnListing('leads'));
    }

    public function getName()
    {
        return CrmNameResource::collection(Crm::all());
    }

}
