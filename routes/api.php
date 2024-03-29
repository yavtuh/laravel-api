<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('lead/create', [\App\Http\Controllers\Api\V1\Lead\LeadController::class, 'store'])->middleware(['api_key']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return new App\Http\Resources\User\UserResource($request->user());
    });
    Route::group(['middleware' => ['role:admin']], function () {
        Route::apiResource('users', \App\Http\Controllers\Api\V1\User\UserController::class);
        Route::apiResource('crms', \App\Http\Controllers\Api\V1\Crm\CrmController::class);
        Route::apiResource('crms/settings', \App\Http\Controllers\Api\V1\Crm\CrmSettingsController::class)->except('index', 'store', 'destroy');
        Route::apiResource('crms/create', \App\Http\Controllers\Api\V1\Crm\CrmCreateController::class)->except('index', 'store', 'destroy');
        Route::apiResource('crms/status', \App\Http\Controllers\Api\V1\Crm\CrmStatusController::class)->except('index', 'store', 'destroy');
        Route::apiResource('crms/headers', \App\Http\Controllers\Api\V1\Crm\CrmHeaderController::class)->except('index', 'store', 'destroy');
        Route::apiResource('crms/responses', \App\Http\Controllers\Api\V1\Crm\CrmResponseController::class)->except('index', 'store', 'destroy');
        Route::apiResource('funnels', \App\Http\Controllers\Api\V1\Funnel\FunnelController::class)->except('show');
        Route::get('getLeadFields', [\App\Http\Controllers\Api\V1\Crm\CrmController::class, 'getLeadFields']);
        Route::get('getNameCrms', [\App\Http\Controllers\Api\V1\Crm\CrmController::class, 'getName']);
    });
    Route::post('leads/sent', [\App\Http\Controllers\Api\V1\Lead\LeadController::class, 'sent']);
    Route::get('leads/filterData',  [\App\Http\Controllers\Api\V1\Lead\LeadController::class, 'filterData']);
    Route::delete('leads', [\App\Http\Controllers\Api\V1\Lead\LeadController::class, 'destroy']);
    Route::apiResource('leads', \App\Http\Controllers\Api\V1\Lead\LeadController::class)->only('index', 'show');


});








