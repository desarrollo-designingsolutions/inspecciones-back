<?php

use App\Http\Controllers\EmergencyElementController;
use Illuminate\Support\Facades\Route;

//Rutas protegidas
Route::middleware(["check.permission:menu.emergency.element"])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | EmergencyElement
    |--------------------------------------------------------------------------
    */

    Route::get('/emergency-element/list', [EmergencyElementController::class, 'list']);

    Route::get('/emergency-element/create', [EmergencyElementController::class, 'create']);

    Route::post('/emergency-element/store', [EmergencyElementController::class, 'store']);

    Route::get('/emergency-element/{id}/edit', [EmergencyElementController::class, 'edit']);

    Route::post('/emergency-element/update/{id}', [EmergencyElementController::class, 'update']);

    Route::delete('/emergency-element/delete/{id}', [EmergencyElementController::class, 'delete']);

    Route::post('/emergency-element/changeStatus', [EmergencyElementController::class, 'changeStatus']);

    Route::post('/emergency-element/excelExport', [EmergencyElementController::class, 'excelExport']);

});
