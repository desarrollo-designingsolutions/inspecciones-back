<?php

use App\Http\Controllers\QueryController;
use Illuminate\Support\Facades\Route;

// Lista de Pais, Departamentos y Ciudades
Route::post('/selectInfiniteCountries', [QueryController::class, 'selectInfiniteCountries']);
Route::get('/selectStates/{country_id}', [QueryController::class, 'selectStates']);
Route::get('/selectCities/{state_id}', [QueryController::class, 'selectCities']);
Route::get('/selectCities/country/{country_id}', [QueryController::class, 'selectCitiesCountry']);
// Lista de Pais, Departamentos y Ciudades

Route::post('/selectInfiniteTypeVehicle', [QueryController::class, 'selectInfiniteTypeVehicle']);
Route::post('/selectInfiniteBrandVehicle', [QueryController::class, 'selectInfiniteBrandVehicle']);
Route::post('/selectInfiniteClient', [QueryController::class, 'selectInfiniteClient']);
Route::post('/selectInfiniteTypeDocument', [QueryController::class, 'selectInfiniteTypeDocument']);
Route::post('/selectInfiniteEmergencyElement', [QueryController::class, 'selectInfiniteEmergencyElement']);
Route::post('/selectInfinitePlateVehicle', [QueryController::class, 'selectInfinitePlateVehicle']);
Route::post('/selectInfiniteUserOperator', [QueryController::class, 'selectInfiniteUserOperator']);
Route::post('/selectInfiniteUserMechanic', [QueryController::class, 'selectInfiniteUserMechanic']);
Route::post('/selectInfiniteUserInspector', [QueryController::class, 'selectInfiniteUserInspector']);
Route::post('/selectResponseStatus', [QueryController::class, 'selectResponseStatus']);
