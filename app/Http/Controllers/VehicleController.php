<?php

namespace App\Http\Controllers;

use App\Exports\VehicleListExport;
use App\Helpers\Constants;
use App\Http\Requests\Vehicle\VehicleStoreRequest;
use App\Http\Resources\Vehicle\VehicleFormResource;
use App\Http\Resources\Vehicle\VehicleListResource;
use App\Http\Resources\Vehicle\VehiclePaginateResource;
use App\Repositories\InspectionTypeGroupRepository;
use App\Repositories\MaintenanceTypeGroupRepository;
use App\Repositories\VehicleDocumentRepository;
use App\Repositories\VehicleEmergencyElementRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\VehicleStructureRepository;
use App\Services\CacheService;
use App\Traits\HttpResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class VehicleController extends Controller
{
    use HttpResponseTrait;

    private $key_redis_project;

    public function __construct(
        protected VehicleRepository $vehicleRepository,
        protected QueryController $queryController,
        protected VehicleStructureRepository $vehicleStructureRepository,
        protected VehicleDocumentRepository $vehicleDocumentRepository,
        protected VehicleEmergencyElementRepository $vehicleEmergencyElementRepository,
        protected MaintenanceTypeGroupRepository $maintenanceTypeGroupRepository,
        protected InspectionTypeGroupRepository $inspectionTypeGroupRepository,
        protected CacheService $cacheService,
    ) {
        $this->key_redis_project = env('KEY_REDIS_PROJECT');
    }

    public function paginate(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $this->vehicleRepository->paginate($request->all());
            $tableData = VehiclePaginateResource::collection($data);

            return [
                'code' => 200,
                'tableData' => $tableData,
                'lastPage' => $data->lastPage(),
                'totalData' => $data->total(),
                'totalPage' => $data->perPage(),
                'currentPage' => $data->currentPage(),
            ];
        });
    }

    public function list(Request $request)
    {
        try {
            $data = $this->vehicleRepository->list($request->all());
            $tableData = VehicleListResource::collection($data);

            return [
                'code' => 200,
                'tableData' => $tableData,
                'lastPage' => $data->lastPage(),
                'totalData' => $data->total(),
                'totalPage' => $data->perPage(),
                'currentPage' => $data->currentPage(),
            ];
        } catch (Throwable $th) {
            return response()->json([
                'code' => 500,
                'message' => Constants::ERROR_MESSAGE_TRYCATCH,
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
    }

    public function create()
    {
        try {

            $selectStates = $this->queryController->selectStates(Constants::COUNTRY_ID);
            $vehicle_structures = $this->vehicleStructureRepository->selectList();
            $type_inspection_inputs = $this->inspectionTypeGroupRepository->typeInspectionInputs();

            return response()->json([
                'code' => 200,
                'vehicle_structures' => $vehicle_structures,
                'type_inspection_inputs' => $type_inspection_inputs,
                ...$selectStates,
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'code' => 500,
                'message' => Constants::ERROR_MESSAGE_TRYCATCH,
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
    }

    public function store(VehicleStoreRequest $request)
    {

        try {
            DB::beginTransaction();

            $post = [
                'id' => $request->input('id'),
                'company_id' => $request->input('company_id'),
                'license_plate' => $request->input('license_plate'),
                'type_vehicle_id' => $request->input('type_vehicle_id'),
                'date_registration' => $request->input('date_registration'),
                'brand_vehicle_id' => $request->input('brand_vehicle_id'),
                'engine_number' => $request->input('engine_number'),
                'state_id' => $request->input('state_id'),
                'city_id' => $request->input('city_id'),
                'model' => $request->input('model'),
                'vin_number' => $request->input('vin_number'),
                'load_capacity' => $request->input('load_capacity'),
                'client_id' => $request->input('client_id'),
                'gross_vehicle_weight' => $request->input('gross_vehicle_weight'),
                'passenger_capacity' => $request->input('passenger_capacity'),
                'number_axles' => $request->input('number_axles'),
                'current_mileage' => $request->input('current_mileage'),
                'have_trailer' => $request->input('have_trailer'),
                'trailer' => $request->input('trailer'),
                'vehicle_structure_id' => $request->input('vehicle_structure_id'),
            ];

            $vehicle = $this->vehicleRepository->store($post);

            $type_groups = json_decode($request->input('type_groups'), 1);

            $vehicle->inspection_group_vehicle()->sync($type_groups);

            // PHOTOS
            if ($request->file('photo_front')) {
                $file = $request->file('photo_front');
                $ruta = 'companies/company_'.$vehicle->company_id.'/vehicle/'.$vehicle->id.$request->input('photo_front');
                $photo_front = $file->store($ruta, 'public');
                $vehicle->photo_front = $photo_front;
                $vehicle->save();
            }

            if ($request->file('photo_rear')) {
                $file = $request->file('photo_rear');
                $ruta = 'companies/company_'.$vehicle->company_id.'/vehicle/'.$vehicle->id.$request->input('photo_rear');
                $photo_rear = $file->store($ruta, 'public');
                $vehicle->photo_rear = $photo_rear;
                $vehicle->save();
            }

            if ($request->file('photo_right_side')) {
                $file = $request->file('photo_right_side');
                $ruta = 'companies/company_'.$vehicle->company_id.'/vehicle/'.$vehicle->id.$request->input('photo_right_side');
                $photo_right_side = $file->store($ruta, 'public');
                $vehicle->photo_right_side = $photo_right_side;
                $vehicle->save();
            }

            if ($request->file('photo_left_side')) {
                $file = $request->file('photo_left_side');
                $ruta = 'companies/company_'.$vehicle->company_id.'/vehicle/'.$vehicle->id.$request->input('photo_left_side');
                $photo_left_side = $file->store($ruta, 'public');
                $vehicle->photo_left_side = $photo_left_side;
                $vehicle->save();
            }

            // EMERGENCY ELEMENTS
            $emergency_elements = json_decode($request->input('emergency_elements'), 1);
            $arrayIds = collect($emergency_elements)->pluck('id');
            $this->vehicleEmergencyElementRepository->deleteArray($arrayIds, $vehicle->id);

            foreach ($emergency_elements as $key => $value) {
                $dataSave = [
                    'id' => $value['id'],
                    'vehicle_id' => $vehicle->id,
                    'emergency_element_id' => $value['emergency_element_id']['value'],
                    'quantity' => $value['quantity'],
                    'expiration_date' => (isset($value['expiration_date']) && ! empty($value['expiration_date']))
                        ? $value['expiration_date']
                        : null,
                ];
                $this->vehicleEmergencyElementRepository->store($dataSave);
            }

            // TYPE DOCUMENTS
            $type_documents = is_string($request->input('type_documents'))
                ? json_decode($request->input('type_documents'), true)
                : $request->input('type_documents');

            $arrayIds = collect($type_documents)->pluck('id');
            $this->vehicleDocumentRepository->deleteArray($arrayIds, $vehicle->id);

            $cantfiles = $request->input('cantfiles');

            for ($i = 0; $i < $cantfiles; $i++) {
                $idFile = $request->input('file_id'.$i) == 'null' ? null : $request->input('file_id'.$i);

                $dataSave = [
                    'id' => $idFile,
                    'vehicle_id' => $vehicle->id,
                    'type_document_id' => $request->input('file_type_document_id'.$i),
                    'document_number' => $request->input('file_document_number'.$i),
                    'date_issue' => $request->input('file_date_issue'.$i),
                    'expiration_date' => $request->input('file_expiration_date'.$i),
                ];
                if ($request->hasFile('file_photo'.$i)) {
                    $file = $request->file('file_photo'.$i);
                    $company_id = $request->input('company_id');

                    // Define la ruta donde se guardará el archivo
                    $ruta = "companies/company_{$company_id}/vehicle/{$vehicle->id}/document";

                    // Guarda el archivo con el nombre original
                    $path = $file->store($ruta, 'public');
                    $dataSave['photo'] = $path;
                }
                $this->vehicleDocumentRepository->store($dataSave);
            }

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Vehículo agregado correctamente', 'data' => $vehicle]);
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'code' => 500,
                'message' => Constants::ERROR_MESSAGE_TRYCATCH,
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $selectStates = $this->queryController->selectStates(Constants::COUNTRY_ID);
            $vehicle_structures = $this->vehicleStructureRepository->selectList();
            $vehicle = $this->vehicleRepository->find($id);
            $form = new VehicleFormResource($vehicle);
            $type_inspection_inputs = $this->inspectionTypeGroupRepository->typeInspectionInputs();

            return response()->json([
                'code' => 200,
                'form' => $form,
                'vehicle_structures' => $vehicle_structures,
                'type_inspection_inputs' => $type_inspection_inputs,
                ...$selectStates,
            ]);
        } catch (Throwable $th) {

            return response()->json([
                'code' => 500,
                'message' => Constants::ERROR_MESSAGE_TRYCATCH,
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
    }

    public function update(VehicleStoreRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $post = [
                'id' => $request->input('id'),
                'company_id' => $request->input('company_id'),
                'license_plate' => $request->input('license_plate'),
                'type_vehicle_id' => $request->input('type_vehicle_id'),
                'date_registration' => $request->input('date_registration'),
                'brand_vehicle_id' => $request->input('brand_vehicle_id'),
                'engine_number' => $request->input('engine_number'),
                'state_id' => $request->input('state_id'),
                'city_id' => $request->input('city_id'),
                'model' => $request->input('model'),
                'vin_number' => $request->input('vin_number'),
                'load_capacity' => $request->input('load_capacity'),
                'client_id' => $request->input('client_id'),
                'gross_vehicle_weight' => $request->input('gross_vehicle_weight'),
                'passenger_capacity' => $request->input('passenger_capacity'),
                'number_axles' => $request->input('number_axles'),
                'current_mileage' => $request->input('current_mileage'),
                'have_trailer' => $request->input('have_trailer'),
                'trailer' => $request->input('trailer'),
                'vehicle_structure_id' => $request->input('vehicle_structure_id'),
            ];

            $vehicle = $this->vehicleRepository->store($post);

            $type_groups = json_decode($request->input('type_groups'), 1);

            $vehicle->inspection_group_vehicle()->sync($type_groups);

            // PHOTOS
            if ($request->file('photo_front')) {
                $file = $request->file('photo_front');
                $ruta = 'companies/company_'.$vehicle->company_id.'/vehicle/'.$vehicle->id.$request->input('photo_front');
                $photo_front = $file->store($ruta, 'public');
                $vehicle->photo_front = $photo_front;
                $vehicle->save();
            }

            if ($request->file('photo_rear')) {
                $file = $request->file('photo_rear');
                $ruta = 'companies/company_'.$vehicle->company_id.'/vehicle/'.$vehicle->id.$request->input('photo_rear');
                $photo_rear = $file->store($ruta, 'public');
                $vehicle->photo_rear = $photo_rear;
                $vehicle->save();
            }

            if ($request->file('photo_right_side')) {
                $file = $request->file('photo_right_side');
                $ruta = 'companies/company_'.$vehicle->company_id.'/vehicle/'.$vehicle->id.$request->input('photo_right_side');
                $photo_right_side = $file->store($ruta, 'public');
                $vehicle->photo_right_side = $photo_right_side;
                $vehicle->save();
            }

            if ($request->file('photo_left_side')) {
                $file = $request->file('photo_left_side');
                $ruta = 'companies/company_'.$vehicle->company_id.'/vehicle/'.$vehicle->id.$request->input('photo_left_side');
                $photo_left_side = $file->store($ruta, 'public');
                $vehicle->photo_left_side = $photo_left_side;
                $vehicle->save();
            }

            // EMERGENCY ELEMENTS
            $emergency_elements = json_decode($request->input('emergency_elements'), 1);
            $arrayIds = collect($emergency_elements)->pluck('id');
            $this->vehicleEmergencyElementRepository->deleteArray($arrayIds, $vehicle->id);

            foreach ($emergency_elements as $key => $value) {
                $dataSave = [
                    'id' => $value['id'],
                    'vehicle_id' => $vehicle->id,
                    'emergency_element_id' => $value['emergency_element_id']['value'],
                    'quantity' => $value['quantity'],
                    'expiration_date' => (isset($value['expiration_date']) && ! empty($value['expiration_date']))
                        ? $value['expiration_date']
                        : null,
                ];
                $this->vehicleEmergencyElementRepository->store($dataSave);
            }

            // TYPE DOCUMENTS

            logMessage($request->input('type_documents'));

            $type_documents = is_string($request->input('type_documents'))
                ? json_decode($request->input('type_documents'), true)
                : $request->input('type_documents');

            $arrayIds = collect($type_documents)->pluck('id');
            $this->vehicleDocumentRepository->deleteArray($arrayIds, $vehicle->id);

            $cantfiles = $request->input('cantfiles');

            for ($i = 0; $i < $cantfiles; $i++) {
                $idFile = $request->input('file_id'.$i) == 'null' ? null : $request->input('file_id'.$i);

                $dataSave = [
                    'id' => $idFile,
                    'vehicle_id' => $vehicle->id,
                    'type_document_id' => $request->input('file_type_document_id'.$i),
                    'document_number' => $request->input('file_document_number'.$i),
                    'date_issue' => $request->input('file_date_issue'.$i),
                    'expiration_date' => $request->input('file_expiration_date'.$i),
                ];
                if ($request->hasFile('file_photo'.$i)) {
                    $file = $request->file('file_photo'.$i);
                    $company_id = $request->input('company_id');

                    // Define la ruta donde se guardará el archivo
                    $ruta = "companies/company_{$company_id}/vehicle/{$vehicle->id}/document";

                    // Guarda el archivo con el nombre original
                    $path = $file->store($ruta, 'public');
                    $dataSave['photo'] = $path;
                }
                $this->vehicleDocumentRepository->store($dataSave);
            }

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Vehículo modificado correctamente', 'data' => $vehicle]);
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'code' => 500,
                'message' => Constants::ERROR_MESSAGE_TRYCATCH,
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $vehicle = $this->vehicleRepository->find($id);
            if ($vehicle) {

                if (
                    $vehicle->inspection()->exists() || $vehicle->maintenance()->exists()
                ) {
                    throw new \Exception('No se puede eliminar el registro, por que tiene relación de datos en otros módulos');
                }

                $vehicle->type_documents()->delete();
                $vehicle->emergency_elements()->delete();
                $vehicle->delete();
                $msg = 'Registro eliminado correctamente';
            } else {
                $msg = 'El registro no existe';
            }
            DB::commit();

            return response()->json(['code' => 200, 'message' => $msg]);
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'code' => 500,
                'message' => $th->getMessage(),
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
    }

    public function changeStatus(Request $request)
    {
        try {
            DB::beginTransaction();

            $model = $this->vehicleRepository->changeState($request->input('id'), strval($request->input('value')), $request->input('field'));

            ($model->is_active == 1) ? $msg = 'habilitado(a)' : $msg = 'inhabilitado(a)';

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Vehículo '.$msg.' con éxito']);
        } catch (Throwable $th) {
            DB::rollback();

            return response()->json([
                'code' => 500,
                'message' => Constants::ERROR_MESSAGE_TRYCATCH,
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
    }

    public function excelExport(Request $request)
    {
        return $this->execute(function () use ($request) {
            $request['typeData'] = 'all';

            $data = $this->vehicleRepository->paginate($request->all());

            $excel = Excel::raw(new VehicleListExport($data), \Maatwebsite\Excel\Excel::XLSX);

            $excelBase64 = base64_encode($excel);

            return [
                'code' => 200,
                'excel' => $excelBase64,
            ];
        });
    }

    public function validateLicensePlate(Request $request)
    {
        try {
            $request->validate([
                'license_plate' => 'required|string',
            ]);

            $exists = $this->vehicleRepository->validateLicensePlate($request->all());

            return [
                'message_licences' => 'La número de placa ya existe.',
                'exists' => $exists,
            ];
        } catch (Throwable $th) {
            return response()->json([
                'code' => 500,
                'message' => Constants::ERROR_MESSAGE_TRYCATCH,
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
    }

    public function pdfExport(Request $request)
    {
        $this->cacheService->clearByPrefix($this->key_redis_project.'string:vehicles_find_'.$request->input('id').'_*');

        return $this->execute(function () use ($request) {
            $vehicle = $this->vehicleRepository->find($request->input('id'), ['maintenance']);

            $maintenanceType = $this->maintenanceTypeGroupRepository->list(
                [
                    'typeData' => 'all',
                    'maintenance_type_id' => 1,
                    'sortBy' => json_encode([
                        [
                            'key' => 'order',
                            'order' => 'asc',
                        ],
                    ]),
                ],
                select: ['id', 'name']
            );

            $table = [];
            $table[0][0] = 'Año';
            $table[0][1] = 'Mes';

            foreach ($maintenanceType->sortBy('order') as $key => $value) {
                $table[0][$key + 2] = $value->name;
            }

            // Agrupar mantenimientos por Año y Mes
            $groupedMaintenances = collect($vehicle->maintenance)
                ->groupBy(fn ($item) => Carbon::parse($item->maintenance_date)->format('Y-m'))
                ->sortKeys(); // ascendente: 2025-01, 2025-04, 2025-06

            $rowIndex = 1;
            foreach ($groupedMaintenances as $yearMonth => $maintenancesInGroup) {
                $year = Carbon::parse($yearMonth.'-01')->format('Y');
                $month = Carbon::parse($yearMonth.'-01')->format('m');

                $table[$rowIndex][0] = $year;
                $table[$rowIndex][1] = $month;

                // Inicializar contadores por cada tipo de mantenimiento
                for ($columnIndex = 2; $columnIndex < count($table[0]); $columnIndex++) {
                    $count = 0;
                    $maintenanceTypeName = $table[0][$columnIndex];

                    foreach ($maintenancesInGroup as $maintenance) {
                        $maintenanceTypeGroup = $maintenance->maintenanceType->maintenanceTypeGroups
                            ->where('name', $maintenanceTypeName)
                            ->first();

                        if ($maintenanceTypeGroup) {
                            foreach ($maintenanceTypeGroup->maintenanceTypeInputs as $input) {
                                foreach ($input->maintenanceInputResponses as $response) {
                                    if ($response->maintenance_id === $maintenance->id) {
                                        if (
                                            empty($response->type) &&
                                            ! empty($response->type_maintenance)
                                        ) {
                                            $count++;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $table[$rowIndex][$columnIndex] = $count;
                }

                $rowIndex++;
            }

            $data = [
                'vehicle' => $vehicle,
                'maintenance' => $vehicle->maintenance,
                'table' => $table,
            ];

            $pdf = $this->vehicleRepository->pdf('Exports.Vehicle.VehicleListExportPDF', $data, is_stream: false);

            if (empty($pdf)) {
                throw new \Exception('Error al generar el PDF');
            }

            $content = $pdf->getOriginalContent();

            $filename = 'temp/pdf/hoja_de_vida_'.$vehicle->license_plate.'.pdf';
            Storage::disk('public')->put($filename, $content);
            $path = Storage::disk('public')->url($filename);

            return [
                'code' => 200,
                'path' => $path,
            ];
        });
    }
}
