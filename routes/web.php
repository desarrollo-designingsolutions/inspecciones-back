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


// Route::get('/prueba', function () {
//     $inspectionRepository = new \App\Repositories\InspectionRepository(new Inspection);
//     $vehicleRepository = new \App\Repositories\VehicleRepository(new Vehicle);

//     $inspection = $inspectionRepository->find('9e23e960-6fc3-4e96-a56d-8ea58c0c47a8');
//     $vehicle = $vehicleRepository->find($inspection->vehicle->id);

//     $tabs = InspectionTypeGroup::select(['id', 'name'])
//         ->with([
//             'inspectionTypeInputs:id,inspection_type_group_id,name',
//             'inspectionTypeInputs.inspectionInputResponses:id,inspection_type_input_id,response,observation,inspection_id',
//             'inspectionTypeInputs.inspectionInputResponses' => function ($query) use ($inspection) {
//                 $query->where('inspection_id', $inspection->id);
//             },
//         ])
//         ->where('inspection_type_id', $inspection['inspection_type_id'])->get();

//      $data = [
//         'inspection_type_id' => $inspection['inspection_type_id'],
//         'inspection_date' => Carbon::parse($inspection['inspection_date'])->format('d-m-Y'),
//         'city' => ucfirst($inspection->city->name),
//         'operator' => [
//             'name' => $inspection->user_operator->name,
//             'document' => $inspection->user_operator->document,
//             'license' => $inspection->user_operator->license,
//         ],
//         'vehicle' => [
//             'id' => $vehicle->id,
//             'license_plate' => $vehicle->license_plate,
//             'brand_vehicle_name' => $vehicle->brand_vehicle?->name,
//             'model' => $vehicle->model,
//             'vehicle_structure_name' => $vehicle->vehicle_structure?->name,
//         ],
//         'documents' => $vehicle->type_documents->map(function ($item) use ($inspection) {
//             $inspectionDocumentVerification = $inspection->inspectionDocumentVerifications->where('vehicle_document_id', $item->id)->first();
//             $original = 'N/A';
//             if ($inspectionDocumentVerification) {
//                 $original = $inspectionDocumentVerification->original ? 'S' : 'N';
//             }
//             return [
//                 'name' => $item->type_document?->name,
//                 'document' => $item->document_number,
//                 'expiration_date' => Carbon::parse($item->expiration_date)->format('d-m-Y'),
//                 'original' => $original,

//             ];
//         }),
//         'general_comment' => $inspection->general_comment,
//         'getResponseTypeInspection' => getResponseTypeInspection($inspection['inspection_type_id']),
//         'inspectionInputResponses' => $tabs->map(function ($item) use ($inspection) {

//             logMessage($inspection->inspection_type_id);
//             return [
//                 'id' => $item->id,
//                 'name' => $item->name,
//                 'inspectionTypeInputs' => $item->inspectionTypeInputs->map(function ($input) use ($inspection) {
//                     $getResponseTypeInspection = getResponseTypeInspection($inspection->inspection_type_id);


//                     $responses = [];
//                     $response = $input->inspectionInputResponses->first();
//                     logMessage($response);
//                     if ($inspection->inspection_type_id == 1  &&  isset($response->response['value'])) {
//                         $decodedResponse = json_decode($response->response, true);

//                         $response['response'] = $decodedResponse['value'];
//                     }

//                     foreach ($getResponseTypeInspection as $key => $value) {
//                         $responses[$key] = '';

//                         if ($value['value'] == $response['response']) {
//                             $responses[$key] = 'X';
//                         }
//                     }

//                     return [
//                         'name' => $input->name,
//                         'responses' => $responses,
//                         'observation' => $response['observation'],
//                     ];
//                 }),
//             ];
//         }),
//     ];



//     $pdf = $inspectionRepository->pdf('Exports.Inspection.InspectionExportPDF', $data);




//     return $pdf;
// });
