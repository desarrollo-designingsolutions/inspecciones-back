<?php

use App\Http\Resources\Inspection\InspectionPDFGetVehicleDataResource;
use App\Models\Inspection;
use App\Models\InspectionTypeGroup;
use App\Models\Vehicle;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/prueba', function () {
    $inspectionRepository = new \App\Repositories\InspectionRepository(new Inspection);
    $vehicleRepository = new \App\Repositories\VehicleRepository(new Vehicle);

    $data = $inspectionRepository->find('9e2264d4-8b37-45dc-8f79-0912506d732b');
    $vehicle = $vehicleRepository->find($data->vehicle->id);

    $tabs = InspectionTypeGroup::select(['id', 'name'])
        ->with([
            'inspectionTypeInputs:id,inspection_type_group_id,name',
            'inspectionTypeInputs.inspectionInputResponses:id,inspection_type_input_id,response,inspection_id',
            'inspectionTypeInputs.inspectionInputResponses' => function ($query) {
                $query->where('inspection_id', '9e2264d4-8b37-45dc-8f79-0912506d732b');
            },
        ])
        ->where('inspection_type_id', $data['inspection_type_id'])->get();

       $data2 = [
        'inspection_type_id' => $data['inspection_type_id'],
        'inspection_date' => Carbon::parse($data['inspection_date'])->format('d-m-Y'),
        'city' => ucfirst($data->city->name),
        'operator' => [
            'name' => $data->user_operator->name,
            'document' => $data->user_operator->document,
            'license' => $data->user_operator->license,
        ],
        'vehicle' => [
            'id' => $vehicle->id,
            'license_plate' => $vehicle->license_plate,
            'brand_vehicle_name' => $vehicle->brand_vehicle?->name,
            'model' => $vehicle->model,
            'vehicle_structure_name' => $vehicle->vehicle_structure?->name,
        ],
        'documents' => $vehicle->type_documents->map(function ($item) use ($data) {
            $inspectionDocumentVerification = $data->inspectionDocumentVerifications->where('vehicle_document_id', $item->id)->first();
            $original = 'N/A';
            if ($inspectionDocumentVerification) {
                $original = $inspectionDocumentVerification->original ? 'S' : 'N';
            }
            return [
                'name' => $item->type_document?->name,
                'document' => $item->document_number,
                'expiration_date' => Carbon::parse($item->expiration_date)->format('d-m-Y'),
                'original' => $original,

            ];
        }),
        'general_comment' => $data->general_comment,
        'getResponseVehicle' => getResponseVehicle(),
        'inspectionInputResponses' => $tabs->map(function ($item) {
            return [
                'name' => $item->name,
                'inspectionTypeInputs' => $item->inspectionTypeInputs->map(function ($input) {

                    $getResponseVehicle = getResponseVehicle();

                    $responses = [];
                    $response = $input->inspectionInputResponses;
                    logMessage($input->inspectionInputResponses);
                    foreach ($getResponseVehicle as $key => $value) {
                        $responses[$key] = '';
                        if (isset($value['value']) && isset($response['response']) && $value['value'] == $response['response']) {
                            $responses[$key] = 'X';
                        }
                    }

                    return [
                        'name' => $input->name,
                        'responses' => $responses,
                    ];
                }),
            ];
        }),
    ];



    $pdf = $inspectionRepository->pdf('Exports.Inspection.InspectionExportPDF', $data2);




    return $pdf;
});
