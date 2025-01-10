<?php

use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

//Rutas protegidas
Route::middleware(['check.permission:vehicle.list'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Vehicle
    |--------------------------------------------------------------------------
    */

    Route::get('/vehicle/list', [VehicleController::class, 'list']);

    Route::get('/vehicle/create', [VehicleController::class, 'create']);

    Route::post('/vehicle/store', [VehicleController::class, 'store']);

    Route::get('/vehicle/{id}/edit', [VehicleController::class, 'edit']);

    Route::post('/vehicle/update/{id}', [VehicleController::class, 'update']);

    Route::delete('/vehicle/delete/{id}', [VehicleController::class, 'delete']);

    Route::post('/vehicle/changeStatus', [VehicleController::class, 'changeStatus']);

    Route::post('/vehicle/excelExport', [VehicleController::class, 'excelExport']);

    Route::post('/vehicle/validateLicensePlate', [VehicleController::class, 'validateLicensePlate']);

});
