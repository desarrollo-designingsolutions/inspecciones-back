<?php

use App\Http\Controllers\BrandVehicleController;
use Illuminate\Support\Facades\Route;

//Rutas protegidas
Route::middleware(['check.permission:menu.brand.vehicle'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | BrandVehicle
    |--------------------------------------------------------------------------
    */

    Route::get('/brand-vehicle/paginate', [BrandVehicleController::class, 'paginate']);

    Route::get('/brand-vehicle/list', [BrandVehicleController::class, 'list']);

    Route::get('/brand-vehicle/create', [BrandVehicleController::class, 'create']);

    Route::post('/brand-vehicle/store', [BrandVehicleController::class, 'store']);

    Route::get('/brand-vehicle/{id}/edit', [BrandVehicleController::class, 'edit']);

    Route::post('/brand-vehicle/update/{id}', [BrandVehicleController::class, 'update']);

    Route::delete('/brand-vehicle/delete/{id}', [BrandVehicleController::class, 'delete']);

    Route::post('/brand-vehicle/changeStatus', [BrandVehicleController::class, 'changeStatus']);

    Route::get('/brand-vehicle/excelExport', [BrandVehicleController::class, 'excelExport']);

});
