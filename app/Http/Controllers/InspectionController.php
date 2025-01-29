<?php

namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Http\Requests\Inspection\InspectionStoreRequest;
use App\Http\Resources\Inspection\InspectionFormResource;
use App\Http\Resources\Inspection\InspectionGetVehicleDataResource;
use App\Http\Resources\Inspection\InspectionListResource;
use App\Models\InspectionInputResponse;
use App\Models\InspectionTypeGroup;
use App\Repositories\InspectionDocumentVerificationRepository;
use App\Repositories\InspectionInputResponseRepository;
use App\Repositories\InspectionRepository;
use App\Repositories\InspectionTypeGroupRepository;
use App\Repositories\InspectionTypeRepository;
use App\Repositories\VehicleRepository;
use App\Traits\HttpTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class InspectionController extends Controller
{
    use HttpTrait;

    public function __construct(
        protected InspectionRepository $inspectionRepository,
        protected InspectionTypeRepository $inspectionTypeRepository,
        protected InspectionTypeGroupRepository $inspectionTypeGroupRepository,
        protected InspectionInputResponseRepository $inspectionInputResponseRepository,
        protected VehicleRepository $vehicleRepository,
        protected InspectionDocumentVerificationRepository $inspectionDocumentVerificationRepository,
        protected QueryController $queryController,
    ) {}

    public function list(Request $request)
    {
        return $this->execute(function () use ($request) {

            $data = $this->inspectionRepository->list($request->all());

            $tableData = InspectionListResource::collection($data);

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


    public function create($inspection_type_id)
    {
        return $this->execute(function () use ($inspection_type_id) {

            $data = $this->loadTabs($inspection_type_id);

            return [
                'code' => 200,
                ...$data,
            ];
        });
    }

    public function store(InspectionStoreRequest $request)
    {
        return $this->runTransaction(function () use ($request) {

            return $request;
            $fields = ["vehicle_id", "inspection_type_id", "user_id", "state_id", "city_id", "general_comment", "inspection_date", "company_id"];

            $post1 = $request->only($fields);

            $inspection = $this->inspectionRepository->store($post1);

            $inspection = $this->inspectionDocumentVerificationRepository->store($post1);


            $post2 = $request->except($fields);

            foreach ($post2 as $key => $value) {
                $this->inspectionInputResponseRepository->store([
                    'inspection_id' => $inspection->id,
                    'inspection_type_input_id' => $key,
                    'user_id' => $post1['user_id'],
                    'response' => $value,
                ]);
            }

            return [
                'code' => 200,
                'message' => 'Inspección agregado correctamente',
                'data' => $inspection
            ];
        });
    }

    public function edit($id)
    {
        return $this->execute(function () use ($id) {

            $inspection = $this->inspectionRepository->find($id);

            $form = new InspectionFormResource($inspection);

            $data = $this->loadTabs($inspection->inspection_type_id);

            return [
                'code' => 200,
                'form' => $form,
                ...$data,
            ];
        });
    }

    public function update(InspectionStoreRequest $request, $id)
    {
        return $this->runTransaction(function () use ($request, $id) {

            $fields = ["id", "vehicle_id", "inspection_type_id", "user_id", "state_id", "city_id", "general_comment", "inspection_date", "company_id"];

            $post1 = $request->only($fields);

            $inspection = $this->inspectionRepository->store($post1, $id);

            $post2 = $request->only(['type_documents']);
            foreach ($post2['type_documents'] as $key => $value) {
                $inspectionDocumentVerification = $this->inspectionDocumentVerificationRepository->updateOrCreate([
                    'inspection_id' => $inspection->id,
                    'vehicle_document_id' => $value['id'],
                ], [
                    'original' => $value['response'],
                ]);
            }
            $post3 = $request->except([...$fields, ...['type_documents']]);

            foreach ($post3 as $key => $value) {

                $this->inspectionInputResponseRepository->updateOrCreate(
                    [
                        'inspection_id' => $inspection->id,
                        'inspection_type_input_id' => $key,
                    ],
                    [
                        'user_id' => $post1['user_id'],
                        'response' => $value,

                    ]
                );
            }

            return [
                'code' => 200,
                'message' => 'Inspección modificada correctamente',
                'data' => $inspection
            ];
        });
    }

    public function delete($id)
    {
        return $this->runTransaction(function () use ($id) {
            $inspection = $this->inspectionRepository->find($id);

            if ($inspection) {
                $inspection->delete();
                $msg = 'Registro eliminado correctamente';
            } else {
                $msg = 'El registro no existe';
            }
            return response()->json(
                [
                    'code' => 200,
                    'message' => $msg
                ]
            );
        });
    }

    public function loadBtnCreate()
    {
        return $this->execute(function () {

            $inspection_type = $this->inspectionTypeRepository->list(
                [
                    'typeData' => 'all',
                    'sortBy' => json_encode([
                        [
                            'key' => 'order',
                            'order' => 'asc',
                        ]
                    ])
                ],
                select: ['id', 'name']
            );

            return [
                'code' => 200,
                'inspection_type' => $inspection_type,
            ];
        });
    }

    public function loadTabs($inspection_type_id)
    {


        $tabs = $this->inspectionTypeGroupRepository->list(
            [
                'typeData' => 'all',
                'inspection_type_id' => $inspection_type_id,
                'sortBy' => json_encode([
                    [
                        'key' => 'order',
                        'order' => 'asc',
                    ]
                ])
            ],
            with: ['inspectionTypeInputs'],
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
            'name' => 'Informacion General',
            'show' => true,
            'errorsValidations' => false,
            'order' => 0,
        ]);

        $selectStates = $this->queryController->selectStates(Constants::COUNTRY_ID);

        $responseDocument = getResponseDocument();
        $responseVehicle = getResponseVehicle();
        return [
            'tabs' => $tabs,
            'responseDocument' => $responseDocument,
            'responseVehicle' => $responseVehicle,
            ...$selectStates,
        ];
    }

    public function getVehicleInfo($vehicle_id)
    {
        return $this->execute(function () use ($vehicle_id) {

            $vehicle = $this->vehicleRepository->find($vehicle_id);

            $vehicle = new InspectionGetVehicleDataResource($vehicle);

            return [
                'code' => 200,
                'vehicle' => $vehicle,
            ];
        });
    }
}
