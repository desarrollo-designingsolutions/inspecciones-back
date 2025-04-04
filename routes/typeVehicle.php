<?php

use App\Http\Controllers\TypeVehicleController;
use Illuminate\Support\Facades\Route;

// Rutas protegidas
Route::middleware(['check.permission:menu.type.vehicle'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | TypeVehicle
    |--------------------------------------------------------------------------
    */

    Route::get('/type-vehicle/paginate', [TypeVehicleController::class, 'paginate']);

    Route::get('/type-vehicle/list', [TypeVehicleController::class, 'list']);

    Route::get('/type-vehicle/create', [TypeVehicleController::class, 'create']);

    Route::post('/type-vehicle/store', [TypeVehicleController::class, 'store']);

    Route::get('/type-vehicle/{id}/edit', [TypeVehicleController::class, 'edit']);

    Route::post('/type-vehicle/update/{id}', [TypeVehicleController::class, 'update']);

    Route::delete('/type-vehicle/delete/{id}', [TypeVehicleController::class, 'delete']);

    Route::post('/type-vehicle/changeStatus', [TypeVehicleController::class, 'changeStatus']);

    Route::get('/type-vehicle/excelExport', [TypeVehicleController::class, 'excelExport']);

});
