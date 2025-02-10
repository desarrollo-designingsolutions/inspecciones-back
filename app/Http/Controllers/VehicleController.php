<?php

namespace App\Http\Controllers;

use App\Exports\VehicleListExport;
use App\Helpers\Constants;
use App\Http\Requests\Vehicle\VehicleStoreRequest;
use App\Http\Resources\Vehicle\VehicleFormResource;
use App\Http\Resources\Vehicle\VehicleListResource;
use App\Repositories\MaintenanceRepository;
use App\Repositories\MaintenanceTypeGroupRepository;
use App\Repositories\VehicleDocumentRepository;
use App\Repositories\VehicleEmergencyElementRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\VehicleStructureRepository;
use App\Traits\HttpTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class VehicleController extends Controller
{
    use HttpTrait;

    public function __construct(
        protected VehicleRepository $vehicleRepository,
        protected QueryController $queryController,
        protected VehicleStructureRepository $vehicleStructureRepository,
        protected VehicleDocumentRepository $vehicleDocumentRepository,
        protected VehicleEmergencyElementRepository $vehicleEmergencyElementRepository,
        protected MaintenanceTypeGroupRepository $maintenanceTypeGroupRepository,
    ) {}

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

            return response()->json([
                'code' => 200,
                'vehicle_structures' => $vehicle_structures,
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

            $post = $request->except(['photo_front', 'photo_rear', 'photo_right_side', 'photo_left_side', 'type_documents', 'emergency_elements']);

            $vehicle = $this->vehicleRepository->store($post);

            //PHOTOS
            if ($request->file('photo_front')) {
                $file = $request->file('photo_front');
                $ruta = 'companies/company_' . $vehicle->company_id . '/vehicle/' . $vehicle->id . $request->input('photo_front');
                $photo_front = $file->store($ruta, 'public');
                $vehicle->photo_front = $photo_front;
                $vehicle->save();
            }

            if ($request->file('photo_rear')) {
                $file = $request->file('photo_rear');
                $ruta = 'companies/company_' . $vehicle->company_id . '/vehicle/' . $vehicle->id . $request->input('photo_rear');
                $photo_rear = $file->store($ruta, 'public');
                $vehicle->photo_rear = $photo_rear;
                $vehicle->save();
            }

            if ($request->file('photo_right_side')) {
                $file = $request->file('photo_right_side');
                $ruta = 'companies/company_' . $vehicle->company_id . '/vehicle/' . $vehicle->id . $request->input('photo_right_side');
                $photo_right_side = $file->store($ruta, 'public');
                $vehicle->photo_right_side = $photo_right_side;
                $vehicle->save();
            }

            if ($request->file('photo_left_side')) {
                $file = $request->file('photo_left_side');
                $ruta = 'companies/company_' . $vehicle->company_id . '/vehicle/' . $vehicle->id . $request->input('photo_left_side');
                $photo_left_side = $file->store($ruta, 'public');
                $vehicle->photo_left_side = $photo_left_side;
                $vehicle->save();
            }

            $type_documents = json_decode($request->input('type_documents'), 1);
            $arrayIds = collect($type_documents)->pluck('id');
            $this->vehicleDocumentRepository->deleteArray($arrayIds, $vehicle->id);

            foreach ($type_documents as $key => $value) {
                $dataSave = [
                    'id' => $value['id'],
                    'vehicle_id' => $vehicle->id,
                    'type_document_id' => $value['type_document_id']['value'],
                    'document_number' => $value['document_number'],
                    'date_issue' => $value['date_issue'],
                    'expiration_date' => $value['expiration_date'],
                ];
                $this->vehicleDocumentRepository->store($dataSave);
            }

            $emergency_elements = json_decode($request->input('emergency_elements'), 1);
            $arrayIds = collect($emergency_elements)->pluck('id');
            $this->vehicleDocumentRepository->deleteArray($arrayIds, $vehicle->id);

            foreach ($emergency_elements as $key => $value) {
                $dataSave = [
                    'id' => $value['id'],
                    'vehicle_id' => $vehicle->id,
                    'emergency_element_id' => $value['emergency_element_id']['value'],
                    'quantity' => $value['quantity'],
                    'expiration_date' => $value['expiration_date'],
                ];
                $this->vehicleEmergencyElementRepository->store($dataSave);
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

            return response()->json([
                'code' => 200,
                'form' => $form,
                'vehicle_structures' => $vehicle_structures,
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

            $post = $request->except(['photo_front', 'photo_rear', 'photo_right_side', 'photo_left_side', 'type_documents', 'emergency_elements']);

            $vehicle = $this->vehicleRepository->store($post);

            //PHOTOS
            if ($request->file('photo_front')) {
                $file = $request->file('photo_front');
                $ruta = 'companies/company_' . $vehicle->company_id . '/vehicle/' . $vehicle->id . $request->input('photo_front');
                $photo_front = $file->store($ruta, 'public');
                $vehicle->photo_front = $photo_front;
                $vehicle->save();
            }

            if ($request->file('photo_rear')) {
                $file = $request->file('photo_rear');
                $ruta = 'companies/company_' . $vehicle->company_id . '/vehicle/' . $vehicle->id . $request->input('photo_rear');
                $photo_rear = $file->store($ruta, 'public');
                $vehicle->photo_rear = $photo_rear;
                $vehicle->save();
            }

            if ($request->file('photo_right_side')) {
                $file = $request->file('photo_right_side');
                $ruta = 'companies/company_' . $vehicle->company_id . '/vehicle/' . $vehicle->id . $request->input('photo_right_side');
                $photo_right_side = $file->store($ruta, 'public');
                $vehicle->photo_right_side = $photo_right_side;
                $vehicle->save();
            }

            if ($request->file('photo_left_side')) {
                $file = $request->file('photo_left_side');
                $ruta = 'companies/company_' . $vehicle->company_id . '/vehicle/' . $vehicle->id . $request->input('photo_left_side');
                $photo_left_side = $file->store($ruta, 'public');
                $vehicle->photo_left_side = $photo_left_side;
                $vehicle->save();
            }

            $type_documents = json_decode($request->input('type_documents'), 1);
            $arrayIds = collect($type_documents)->pluck('id');
            $this->vehicleDocumentRepository->deleteArray($arrayIds, $vehicle->id);

            foreach ($type_documents as $key => $value) {
                $dataSave = [
                    'id' => $value['id'],
                    'vehicle_id' => $vehicle->id,
                    'type_document_id' => $value['type_document_id']['value'],
                    'document_number' => $value['document_number'],
                    'date_issue' => $value['date_issue'],
                    'expiration_date' => $value['expiration_date'],
                ];
                $this->vehicleDocumentRepository->store($dataSave);
            }

            $emergency_elements = json_decode($request->input('emergency_elements'), 1);
            $arrayIds = collect($emergency_elements)->pluck('id');
            $this->vehicleEmergencyElementRepository->deleteArray($arrayIds, $vehicle->id);

            foreach ($emergency_elements as $key => $value) {
                $dataSave = [
                    'id' => $value['id'],
                    'vehicle_id' => $vehicle->id,
                    'emergency_element_id' => $value['emergency_element_id']['value'],
                    'quantity' => $value['quantity'],
                ];
                $this->vehicleEmergencyElementRepository->store($dataSave);
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
                'message' => Constants::ERROR_MESSAGE_TRYCATCH,
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

            return response()->json(['code' => 200, 'message' => 'Vehículo ' . $msg . ' con éxito']);
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
        try {

            $filter = [
                'typeData' => 'all',
            ];

            $data = $this->vehicleRepository->list([
                ...$filter,
                ...$request->all(),
            ]);

            $excel = Excel::raw(new VehicleListExport($data), \Maatwebsite\Excel\Excel::XLSX);

            $excelBase64 = base64_encode($excel);

            return response()->json(['code' => 200, 'excel' => $excelBase64]);
        } catch (Throwable $th) {
            return response()->json([
                'code' => 500,
                'message' => Constants::ERROR_MESSAGE_TRYCATCH,
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
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

            foreach ($maintenanceType->sortBy("order") as $key => $value) {
                $table[0][$key + 2] = $value->name;
            }

            foreach ($vehicle->maintenance as $key => $currentMaintenance) {
                $rowIndex = $key + 1;
                $table[$rowIndex][] = Carbon::parse($currentMaintenance->maintenance_date)->format('Y');
                $table[$rowIndex][] = Carbon::parse($currentMaintenance->maintenance_date)->format('m');

                // Iterar sobre cada tipo de mantenimiento (columnas)
                for ($columnIndex = 2; $columnIndex < count($table[0]); $columnIndex++) {
                    $count = 0; // Reiniciar contador para cada celda
                    $maintenanceTypeName = $table[0][$columnIndex];

                    // Obtener el tipo de mantenimiento correspondiente a esta columna
                    $maintenanceTypeGroup = $currentMaintenance->maintenanceType->maintenanceTypeGroups
                        ->where('name', $maintenanceTypeName)
                        ->first();

                    if ($maintenanceTypeGroup) {
                        // Contar solo las respuestas asociadas al mantenimiento actual
                        foreach ($maintenanceTypeGroup->maintenanceTypeInputs as $input) {
                            foreach ($input->maintenanceInputResponses as $response) {
                                // Verificar si la respuesta pertenece al mantenimiento actual
                                if ($response->maintenance_id === $currentMaintenance->id) {
                                    if (
                                        !empty($response->type) ||
                                        !empty($response->type_maintenance) ||
                                        !empty($response->comment)
                                    ) {
                                        $count++;
                                    }
                                }
                            }
                        }
                    }

                    $table[$rowIndex][$columnIndex] = $count;
                }
            }

            $data = [
                'vehicle' => $vehicle,
                'maintenance' => $vehicle->maintenance,
                'table' => $table,
            ];

            $pdf = $this->vehicleRepository->pdf('Exports.Vehicle.VehicleListExportPDF', $data, $request->input('pdf_name'));

            $pdfBase64 = base64_encode($pdf);
            return [
                'code' => 200,
                'pdf' => $pdfBase64
            ];
        });
    }
}
