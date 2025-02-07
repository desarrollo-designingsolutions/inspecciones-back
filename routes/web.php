<?php

use App\Http\Resources\Inspection\InspectionPDFGetVehicleDataResource;
use App\Models\Inspection;
use App\Models\InspectionTypeGroup;
use App\Models\MaintenanceTypeGroup;
use App\Models\Vehicle;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/prueba', function () {
    $vehicleRepository = new \App\Repositories\VehicleRepository(new Vehicle);
    $maintenanceTypeGroupRepository = new \App\Repositories\MaintenanceTypeGroupRepository(new MaintenanceTypeGroup);

    $vehicle = $vehicleRepository->find('9e267945-585a-4c3f-8ecf-1dacc3d63d14');

    $maintenance = $vehicle->maintenance;

    $tabs = $vehicle->maintenance::with([
        'maintenanceTypeInputs' => function($query) {
            $query->select('id', 'maintenance_type_group_id', 'name')
                ->with(['maintenanceInputResponses' => function($q) {
                    $q->select('id', 'maintenance_type_input_id', 'type', 'type_maintenance', 'comment');
                }])
                ->withCount(['maintenanceInputResponses as input_filled_count' => function($q) {
                    $q->where(function($query) {
                        $query->whereNotNull('type')
                            ->orWhereNotNull('type_maintenance')
                            ->orWhereNotNull('comment');
                    });
                }]);
        }
    ])->get()->map(function($group) {
        // Calcular total para todo el grupo
        $group->total_filled_responses = $group->maintenanceTypeInputs->sum('input_filled_count');
        
        // Eliminar el contador individual de inputs
        $group->maintenanceTypeInputs->each(function($input) {
            unset($input->input_filled_count);
        });
        
        return $group;
    });
    
     return $data = [
        'vehicle' => $vehicle,
        'maintenance' => $maintenance,
        'maintenance_type' => $tabs,
    ];
    
    
    
    $pdf = $vehicleRepository->pdf('Exports.Vehicle.VehicleListExportPDF', $data);
    
    
    
    
    return $pdf;
});

