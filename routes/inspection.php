<?php

use App\Http\Controllers\InspectionController;
use Illuminate\Support\Facades\Route;

// Rutas protegidas
Route::middleware(['check.permission:inspection.list'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Inspection
    |--------------------------------------------------------------------------
    */

    Route::get('/inspection/paginate', [InspectionController::class, 'paginate']);

    Route::get('/inspection/list', [InspectionController::class, 'list']);

    Route::get('/inspection/loadBtnCreate', [InspectionController::class, 'loadBtnCreate']);

    Route::get('/inspection/getVehicleInfo/{vehicle_id}', [InspectionController::class, 'getVehicleInfo']);

    Route::get('/inspection/create/{inspection_type_id}', [InspectionController::class, 'create']);

    Route::post('/inspection/store', [InspectionController::class, 'store']);

    Route::get('/inspection/{id}/edit', [InspectionController::class, 'edit']);

    Route::post('/inspection/update/{id}', [InspectionController::class, 'update']);

    Route::delete('/inspection/delete/{id}', [InspectionController::class, 'delete']);

    Route::post('/inspection/changeStatus', [InspectionController::class, 'changeStatus']);

    Route::get('/inspection/excelExport', [InspectionController::class, 'excelExport']);

    Route::post('/inspection/pdfExport', [InspectionController::class, 'pdfExport']);

    Route::get('/inspection/showReportInfo/{id}', [InspectionController::class, 'showReportInfo']);

    Route::post('/inspection/excelReportExport', [InspectionController::class, 'excelReportExport']);

    Route::post('/inspection/pdfReportExport', [InspectionController::class, 'pdfReportExport']);

});
