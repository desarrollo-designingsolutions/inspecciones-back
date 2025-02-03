<?php

namespace App\Http\Controllers;

use App\Exports\MaintenanceListExport;
use App\Helpers\Constants;
use App\Http\Requests\Maintenance\MaintenanceStoreRequest;
use App\Http\Resources\Maintenance\MaintenanceFormResource;
use App\Http\Resources\Maintenance\MaintenanceGetVehicleDataResource;
use App\Http\Resources\Maintenance\MaintenanceListResource;
use App\Repositories\MaintenanceInputResponseRepository;
use App\Repositories\MaintenanceRepository;
use App\Repositories\MaintenanceTypeGroupRepository;
use App\Repositories\MaintenanceTypeRepository;
use App\Repositories\VehicleRepository;
use App\Traits\HttpTrait;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MaintenanceController extends Controller
{
    use HttpTrait;

    public function __construct(
        protected MaintenanceRepository $maintenanceRepository,
        protected MaintenanceTypeRepository $maintenanceTypeRepository,
        protected MaintenanceTypeGroupRepository $maintenanceTypeGroupRepository,
        protected MaintenanceInputResponseRepository $maintenanceInputResponseRepository,
        protected VehicleRepository $vehicleRepository,
        protected QueryController $queryController,
    ) {}

    public function list(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $this->maintenanceRepository->list($request->all());
            $tableData = MaintenanceListResource::collection($data);

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

    public function create($maintenance_type_id)
    {
        return $this->execute(function () use ($maintenance_type_id) {

            $data = $this->loadTabs($maintenance_type_id);

            return [
                'code' => 200,
                ...$data,
            ];
        });
    }

    public function store(MaintenanceStoreRequest $request)
    {
        return $this->runTransaction(function () use ($request) {

            $fields = ['id', 'company_id', 'vehicle_id', 'maintenance_type_id', 'user_mechanic_id', 'user_operator_id', 'user_inspector_id', 'state_id', 'city_id', 'maintenance_date', 'mileage', 'general_comment', 'status'];

            $post1 = $request->only($fields);

            $maintenance = $this->maintenanceRepository->store($post1);

            $post2 = $request->except([...$fields, 'have_trailer', 'user_operator_id']);

            foreach ($post2 as $key => $value) {

                $this->maintenanceInputResponseRepository->updateOrCreate(
                    [
                        'maintenance_id' => $maintenance->id,
                        'maintenance_type_input_id' => $key,
                    ],
                    [
                        // 'user_id' => $post1['user_id'],
                        'type' => $value['type'],
                        'type_maintenance' => $value['type_maintenance'],
                        'comment' => $value['comment'],

                    ]
                );
            }

            return [
                'code' => 200,
                'message' => 'Mantenimiento agregado correctamente',
                'data' => $maintenance,
            ];
        });
    }

    public function edit($id)
    {
        return $this->execute(function () use ($id) {
            $maintenance = $this->maintenanceRepository->find($id);

            $form = new MaintenanceFormResource($maintenance);

            $data = $this->loadTabs($maintenance->maintenance_type_id);


            return [
                'code' => 200,
                'form' => $form,
                ...$data,
            ];
        });
    }

    public function update(MaintenanceStoreRequest $request, $id)
    {
        return $this->runTransaction(function () use ($request) {

            $fields = ['id', 'company_id', 'vehicle_id', 'maintenance_type_id', 'user_mechanic_id', 'user_operator_id', 'user_inspector_id', 'state_id', 'city_id', 'maintenance_date', 'mileage', 'general_comment', 'status'];

            $post1 = $request->only($fields);

            $maintenance = $this->maintenanceRepository->store($post1);

            $post2 = $request->except([...$fields, 'have_trailer', 'user_operator_id']);

            foreach ($post2 as $key => $value) {

                $this->maintenanceInputResponseRepository->updateOrCreate(
                    [
                        'maintenance_id' => $maintenance->id,
                        'maintenance_type_input_id' => $key,
                    ],
                    [
                        // 'user_id' => $post1['user_id'],
                        'type' => $value['type'],
                        'type_maintenance' => $value['type_maintenance'],
                        'comment' => $value['comment'],

                    ]
                );
            }

            return [
                'code' => 200,
                'message' => 'Mantenimiento agregado correctamente',
                'data' => $maintenance,
            ];
        });
    }

    public function delete($id)
    {
        return $this->runTransaction(function () use ($id) {
            $maintenance = $this->maintenanceRepository->find($id);
            if ($maintenance) {
                $maintenance->delete();
                $msg = 'Registro eliminado correctamente';
            } else {
                $msg = 'El registro no existe';
            }

            return ['code' => 200, 'message' => $msg];
        });
    }

    public function loadBtnCreate()
    {
        return $this->execute(function () {

            $maintenance_type = $this->maintenanceTypeRepository->list(
                [
                    'typeData' => 'all',
                    'sortBy' => json_encode([
                        [
                            'key' => 'order',
                            'order' => 'asc',
                        ],
                    ]),
                ],
                select: ['id', 'name']
            );

            return [
                'code' => 200,
                'maintenance_type' => $maintenance_type,
            ];
        });
    }

    public function loadTabs($maintenance_type_id)
    {

        $tabs = $this->maintenanceTypeGroupRepository->list(
            [
                'typeData' => 'all',
                'maintenance_type_id' => $maintenance_type_id,
                'sortBy' => json_encode([
                    [
                        'key' => 'order',
                        'order' => 'asc',
                    ],
                ]),
            ],
            with: ['maintenanceTypeInputs'],
            select: ['id', 'name']
        );
        $order = 1;

        foreach ($tabs as $key => $value) {
            $value['show'] = true;
            $value['errorsValidations'] = false;
            $value['order'] = $order;
            $order++;
        }

        $tabs = collect($tabs);

        // Usar prepend() para agregar al inicio
        $tabs->prepend([
            'id' => 0,
            'name' => 'Información General',
            'show' => true,
            'errorsValidations' => false,
            'order' => 0,
        ]);

        $selectStates = $this->queryController->selectStates(Constants::COUNTRY_ID);

        $responseDocument = getResponseDocument();
        $responseMaintenanceInput = getResponseMaintenanceInput();
        $responseTypeMaintenance = getResponseTypeMaintenance();

        return [
            'tabs' => $tabs,
            'responseDocument' => $responseDocument,
            'responseMaintenanceInput' => $responseMaintenanceInput,
            'responseTypeMaintenance' => $responseTypeMaintenance,
            'responseStatus' => getResponseStatus(),
            ...$selectStates,
        ];
    }

    public function getVehicleInfo($vehicle_id)
    {
        return $this->execute(function () use ($vehicle_id) {

            $vehicle = $this->vehicleRepository->find($vehicle_id);

            $vehicle = new MaintenanceGetVehicleDataResource($vehicle);

            return [
                'code' => 200,
                'vehicle' => $vehicle,
            ];
        });
    }

    public function changeStatus(Request $request)
    {
        return $this->runTransaction(function () use ($request) {
            $model = $this->maintenanceRepository->changeState($request->input('id'), strval($request->input('value')), $request->input('field'));

            ($model->is_active == 1) ? $msg = 'habilitado(a)' : $msg = 'inhabilitado(a)';

            return ['code' => 200, 'message' => 'Vehículo ' . $msg . ' con éxito'];
        });
    }

    public function excelExport(Request $request)
    {
        return $this->execute(function () use ($request) {
            $filter = [
                'typeData' => 'all',
            ];

            $data = $this->maintenanceRepository->list([
                ...$filter,
                ...$request->all(),
            ]);

            $excel = Excel::raw(new MaintenanceListExport($data), \Maatwebsite\Excel\Excel::XLSX);

            $excelBase64 = base64_encode($excel);

            return ['code' => 200, 'excel' => $excelBase64];
        });
    }
}
