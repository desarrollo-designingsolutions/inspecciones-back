<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard/countAllData', [DashboardController::class, 'countAllData']);

Route::get('/dashboard/vehicleInfoForCompany', [DashboardController::class, 'vehicleInfoForCompany']);

Route::get('/dashboard/vehicleInspectionsComparison', [DashboardController::class, 'vehicleInspectionsComparison']);

