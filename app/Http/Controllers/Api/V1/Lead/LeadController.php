<?php

namespace App\Http\Controllers\Api\V1\Lead;

use App\Http\Controllers\Controller;
use App\Http\Filters\LeadFilter\LeadFilter;
use App\Http\Requests\Lead\LeadIndexRequest;
use App\Http\Requests\Lead\LeadSentRequest;
use App\Http\Requests\Lead\LeadStoreRequest;
use App\Http\Resources\Lead\LeadBuyerResource;
use App\Http\Resources\Lead\LeadResource;
use App\Models\Crm\Crm;
use App\Models\Funnel\Funnel;
use App\Models\Lead\Lead;
use App\Models\User;
use App\Services\Lead\LeadManagementService;
use Illuminate\Http\Request;

class LeadController extends Controller
{

    public function __construct(protected LeadManagementService $leadService)
    {
    }

    public function index(LeadIndexRequest $request)
    {
        $data = $request->validated();
        $filterLead = app()->make(LeadFilter::class, ['queryParams' => array_filter($data)]);
        $leads = Lead::ofCurrentUser()->filter($filterLead)->latest()->paginate(50);

        if (!auth()->user()->hasRole('admin')) {
            return LeadBuyerResource::collection($leads);
        }

        return LeadResource::collection($leads);
    }

    public function filterData()
    {
        $buyers = User::role('buyer')->select('id', 'name')->get();
        $funnels = Funnel::select('id', 'name')->get();
        $crms = Crm::select('id', 'name')->get();
        $leadStatuses = Lead::query()->pluck('lead_status')->unique()->map(function ($item) {
            return $item === null ? 'Неопределённый' : $item;
        })->values()->toArray();
        $sentStatuses = Lead::query()->pluck('send_status')->unique()->map(function ($item) {
            return $item === null ? 'Неопределённый' : $item;
        })->values()->toArray();
        $sendResult = Lead::query()->pluck('send_result')->unique()->map(function ($item) {
            return $item === null ? 'Неопределённый' : $item;
        })->values()->toArray();
        return response()->json([
            'buyers' => $buyers,
            'funnels' => $funnels,
            'leadStatuses' => $leadStatuses,
            'sentStatuses' => $sentStatuses,
            'sendResult' => $sendResult,
            'crms' => $crms
        ]);
    }

    public function sent(LeadSentRequest $request)
    {
        if ($this->leadService->sentLeads($request->validated())) {
            return response()->noContent();
        } else {
            return response()->json(['error' => 'Не удалось отправить данные'], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LeadStoreRequest $request)
    {
        $lead = $this->leadService->createLead($request->validated());
        return response()->json($lead);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lead = Lead::findOrFail($id);
        return response()->json($lead->response ?? []);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        logs()->info($request->ids);
        Lead::destroy($request->ids);
        return response()->noContent();
    }
}
