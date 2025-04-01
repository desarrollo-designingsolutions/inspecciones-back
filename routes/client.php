<?php

use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

//Rutas protegidas
Route::middleware(['check.permission:client.list'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Client
    |--------------------------------------------------------------------------
    */

    Route::get('/client/paginate', [ClientController::class, 'paginate']);

    Route::get('/client/list', [ClientController::class, 'list']);

    Route::get('/client/create', [ClientController::class, 'create']);

    Route::post('/client/store', [ClientController::class, 'store']);

    Route::get('/client/{id}/edit', [ClientController::class, 'edit']);

    Route::post('/client/update/{id}', [ClientController::class, 'update']);

    Route::delete('/client/delete/{id}', [ClientController::class, 'delete']);

    Route::post('/client/changeStatus', [ClientController::class, 'changeStatus']);

    Route::get('/client/excelExport', [ClientController::class, 'excelExport']);

});
