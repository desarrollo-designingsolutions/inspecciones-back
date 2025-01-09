<?php

use App\Http\Controllers\TypeDocumentController;
use Illuminate\Support\Facades\Route;

//Rutas protegidas
Route::middleware(["check.permission:type.document.list"])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | TypeDocument
    |--------------------------------------------------------------------------
    */

    Route::get('/type-document/list', [TypeDocumentController::class, 'list']);

    Route::get('/type-document/create', [TypeDocumentController::class, 'create']);

    Route::post('/type-document/store', [TypeDocumentController::class, 'store']);

    Route::get('/type-document/{id}/edit', [TypeDocumentController::class, 'edit']);

    Route::post('/type-document/update/{id}', [TypeDocumentController::class, 'update']);

    Route::delete('/type-document/delete/{id}', [TypeDocumentController::class, 'delete']);

    Route::post('/type-document/changeStatus', [TypeDocumentController::class, 'changeStatus']);

    Route::post('/type-document/excelExport', [TypeDocumentController::class, 'excelExport']);

});
