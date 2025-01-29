<?php

use App\Http\Controllers\MaintenanceController;
use Illuminate\Support\Facades\Route;

//Rutas protegidas
Route::middleware(['check.permission:maintenance.list'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Maintenance
    |--------------------------------------------------------------------------
    */

    Route::get('/maintenance/list', [MaintenanceController::class, 'list']);

    Route::get('/maintenance/create', [MaintenanceController::class, 'create']);

    Route::post('/maintenance/store', [MaintenanceController::class, 'store']);

    Route::get('/maintenance/{id}/edit', [MaintenanceController::class, 'edit']);

    Route::post('/maintenance/update/{id}', [MaintenanceController::class, 'update']);

    Route::delete('/maintenance/delete/{id}', [MaintenanceController::class, 'delete']);

    Route::post('/maintenance/changeStatus', [MaintenanceController::class, 'changeStatus']);

    Route::post('/maintenance/excelExport', [MaintenanceController::class, 'excelExport']);
});
