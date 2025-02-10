<?php

use App\Http\Resources\Inspection\InspectionPDFGetVehicleDataResource;
use App\Models\Inspection;
use App\Models\InspectionTypeGroup;
use App\Models\MaintenanceTypeGroup;
use App\Models\Vehicle;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

use function PHPUnit\Framework\isEmpty;

Route::get('/', function () {
    return view('welcome');
});


// Route::get('/prueba', function () {
//     $vehicleRepository = new \App\Repositories\VehicleRepository(new Vehicle);

//     $vehicle = $vehicleRepository->find('9e2260b6-f048-43a3-b5c8-3a8bf1499b22', ['maintenance']);

//     return $maintenanceType = MaintenanceTypeGroup::sortBy("order")->get();

//     $table = [];
//     $table[0][0] = 'Año';
//     $table[0][1] = 'Mes';

//     foreach ($maintenanceType->sortBy("order") as $key => $value) {
//         $table[0][$key + 2] = $value->name;
//     }

//     foreach ($vehicle->maintenance as $key => $currentMaintenance) {
//         $rowIndex = $key + 1;
//         $table[$rowIndex][] = Carbon::parse($currentMaintenance->maintenance_date)->format('Y');
//         $table[$rowIndex][] = Carbon::parse($currentMaintenance->maintenance_date)->format('m');

//         // Iterar sobre cada tipo de mantenimiento (columnas)
//         for ($columnIndex = 2; $columnIndex < count($table[0]); $columnIndex++) {
//             $count = 0; // Reiniciar contador para cada celda
//             $maintenanceTypeName = $table[0][$columnIndex];

//             // Obtener el tipo de mantenimiento correspondiente a esta columna
//             $maintenanceTypeGroup = $currentMaintenance->maintenanceType->maintenanceTypeGroups
//                 ->where('name', $maintenanceTypeName)
//                 ->first();

//             if ($maintenanceTypeGroup) {
//                 // Contar solo las respuestas asociadas al mantenimiento actual
//                 foreach ($maintenanceTypeGroup->maintenanceTypeInputs as $input) {
//                     foreach ($input->maintenanceInputResponses as $response) {
//                         // Verificar si la respuesta pertenece al mantenimiento actual
//                         if ($response->maintenance_id === $currentMaintenance->id) {
//                             if (
//                                 !empty($response->type) ||
//                                 !empty($response->type_maintenance) ||
//                                 !empty($response->comment)
//                             ) {
//                                 $count++;
//                             }
//                         }
//                     }
//                 }
//             }

//             $table[$rowIndex][$columnIndex] = $count;
//         }
//     }

//     $data = [
//         'vehicle' => $vehicle,
//         'maintenance' => $vehicle->maintenance,
//         'table' => $table,
//     ];

//     $pdf = $vehicleRepository->pdf('Exports.Vehicle.VehicleListExportPDF', $data);

//     return $pdf;
// });
