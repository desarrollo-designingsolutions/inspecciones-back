<?php

namespace App\Http\Controllers;

use App\Exports\InspectionListExport;
use App\Helpers\Constants;
use App\Http\Requests\Inspection\InspectionStoreRequest;
use App\Http\Resources\Inspection\InspectionFormResource;
use App\Http\Resources\Inspection\InspectionGetVehicleDataResource;
use App\Http\Resources\Inspection\InspectionListResource;
use App\Jobs\BrevoProcessSendEmail;
use App\Models\InspectionDocumentVerification;
use App\Models\InspectionInputResponse;
use App\Http\Resources\Inspection\InspectionPaginateResource;
use App\Models\InspectionTypeGroup;
use App\Models\User;
use App\Models\Company;
use App\Repositories\InspectionDocumentVerificationRepository;
use App\Repositories\InspectionInputResponseRepository;
use App\Repositories\InspectionRepository;
use App\Repositories\InspectionTypeGroupRepository;
use App\Repositories\InspectionTypeRepository;
use App\Repositories\VehicleRepository;
use App\Traits\HttpResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class InspectionController extends Controller
{
    use HttpResponseTrait;

    public function __construct(
        protected InspectionRepository $inspectionRepository,
        protected InspectionTypeRepository $inspectionTypeRepository,
        protected InspectionTypeGroupRepository $inspectionTypeGroupRepository,
        protected InspectionInputResponseRepository $inspectionInputResponseRepository,
        protected VehicleRepository $vehicleRepository,
        protected InspectionDocumentVerificationRepository $inspectionDocumentVerificationRepository,
        protected QueryController $queryController,
    ) {}

    public function paginate(Request $request)
    {
        return $this->execute(function () use ($request) {

            $data = $this->inspectionRepository->paginate($request->all());

            $tableData = InspectionPaginateResource::collection($data);

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

            $tabs = collect([[
                'id' => 0,
                'name' => 'Información General',
                'show' => true,
                'errorsValidations' => false,
                'order' => 0,
            ]]);

            $selectStates = $this->queryController->selectStates(Constants::COUNTRY_ID);

            $responseDocument = getResponseDocument();
            $responseVehicle = getResponseTypeInspection($inspection_type_id);

            return [
                'code' => 200,
                'tabs' => $tabs,
                'responseDocument' => $responseDocument,
                'responseVehicle' => $responseVehicle,
                ...$selectStates,
            ];
        });
    }

    public function store(InspectionStoreRequest $request)
    {
        return $this->runTransaction(function () use ($request) {

            $fields = ["id", "vehicle_id", "inspection_type_id", "user_inspector_id", "user_operator_id", "state_id", "city_id", "general_comment", "inspection_date", "company_id"];

            $post1 = $request->only($fields);

            $inspection = $this->inspectionRepository->store($post1);
            $vehicle = $this->vehicleRepository->find($post1['vehicle_id']);

            $inpectionGroupsIds = $vehicle->inspection_group_vehicle->where('inspection_type_id', $inspection->inspection_type_id)->pluck('id');

            $inspection->inspection_group_inspection()->sync($inpectionGroupsIds);

            $post2 = $request->only(['type_documents']);
            foreach ($post2['type_documents'] as $key => $value) {
                InspectionDocumentVerification::updateOrCreate([
                    'inspection_id' => $inspection->id,
                    'vehicle_document_id' => $value['id'],
                ], [
                    'original' => $value['original'],
                ]);
            }
            $post3 = $request->except([...$fields, ...['type_documents']]);

            foreach ($post3 as $key => $value) {
                if ($value != null) {
                    InspectionInputResponse::updateOrCreate(
                        [
                            'inspection_id' => $inspection->id,
                            'inspection_type_input_id' => $key,
                        ],
                        [
                            'user_inspector_id' => $post1['user_inspector_id'],
                            'response' => $value['value'] != null ? $value['value'] : '',
                            'observation' => isset($value['observation']) ? $value['observation'] : '',
                        ]
                    );
                }
            }

            $company = Company::where('id', $post1['company_id'])->get()->first();

            $this->sendNotificationGenerateInspection($company, [
                'title' => 'Se ha creado una nueva inspección',
                'type_inspection' => $inspection->inspectionType->name,
                'license_plate' => $inspection->vehicle->license_plate,
                'action_url' => 'Inspection/Inspection-form/' . $inspection->inspection_type_id . '/edit/' . $inspection->id,
            ]);

            return [
                'code' => 200,
                'message' => 'Inspección agregado correctamente',
                'data' => $inspection,
            ];
        });
    }

    public function edit($id)
    {
        return $this->execute(function () use ($id) {

            $tabs = collect([[
                'id' => 0,
                'name' => 'Información General',
                'show' => true,
                'errorsValidations' => false,
                'order' => 0,
            ]]);

            $inspection = $this->inspectionRepository->find($id);

            $form = new InspectionFormResource($inspection);

            $selectStates = $this->queryController->selectStates(Constants::COUNTRY_ID);

            $responseDocument = getResponseDocument();
            $responseVehicle = getResponseTypeInspection($inspection->inspection_type_id);

            return [
                'code' => 200,
                'tabs' => $tabs,
                'form' => $form,
                'responseDocument' => $responseDocument,
                'responseVehicle' => $responseVehicle,
                ...$selectStates,
            ];
        });
    }

    public function update(InspectionStoreRequest $request, $id)
    {
        return $this->runTransaction(function () use ($request, $id) {

            $fields = ['id', 'vehicle_id', 'inspection_type_id', 'user_inspector_id', 'user_operator_id', 'state_id', 'city_id', 'general_comment', 'inspection_date', 'company_id'];

            $post1 = $request->only($fields);

            $inspectionOld = $this->inspectionRepository->find($id);
            $inspection = $this->inspectionRepository->store($post1, $id);

            if ($inspection->vehicle_id != $inspectionOld->vehicle_id) {
                $inspection->inspection_group_inspection()->sync([]);

                $vehicle = $this->vehicleRepository->find($post1['vehicle_id']);

                $inpectionGroupsIds = $vehicle->inspection_group_vehicle->where('inspection_type_id', $inspection->inspection_type_id)->pluck('id');

                $inspection->inspection_group_inspection()->sync($inpectionGroupsIds);
            }

            $post2 = $request->only(['type_documents']);
            foreach ($post2['type_documents'] as $key => $value) {
                InspectionDocumentVerification::updateOrCreate([
                    'inspection_id' => $inspection->id,
                    'vehicle_document_id' => $value['id'],
                ], [
                    'original' => $value['original'],
                ]);
            }
            $post3 = $request->except([...$fields, ...['type_documents']]);

            foreach ($post3 as $key => $value) {
                if ($value != null) {
                    InspectionInputResponse::updateOrCreate(
                        [
                            'inspection_id' => $inspection->id,
                            'inspection_type_input_id' => $key,
                        ],
                        [
                            'user_inspector_id' => $post1['user_inspector_id'],
                            'response' => $value['value'] != null ? $value['value'] : '',
                            'observation' => isset($value['observation']) ? $value['observation'] : '',
                        ]
                    );
                }
            }

            return [
                'code' => 200,
                'message' => 'Inspección modificada correctamente',
                'data' => $inspection,
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

            return [
                'code' => 200,
                'message' => $msg,
            ];
        });
    }

    public function changeStatus(Request $request)
    {
        return $this->runTransaction(function () use ($request) {

            $model = $this->inspectionRepository->changeState($request->input('id'), strval($request->input('value')), $request->input('field'));

            ($model->is_active == 1) ? $msg = 'habilitado(a)' : $msg = 'inhabilitado(a)';

            return ['code' => 200, 'message' => 'Vehículo ' . $msg . ' con éxito'];
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
                        ],
                    ]),
                ],
                select: ['id', 'name']
            );

            return [
                'code' => 200,
                'inspection_type' => $inspection_type,
            ];
        });
    }

    public function getVehicleInfo(Request $request, $vehicle_id)
    {
        return $this->execute(function () use ($request, $vehicle_id) {

            $inspection_id = $request->input('inspection_id', null);

            $vehicle = $this->vehicleRepository->find($vehicle_id);
            $vehicle = new InspectionGetVehicleDataResource($vehicle);

            $form = null;

            if (empty($inspection_id)) {

                $inputs = $vehicle->inspection_group_vehicle
                    ->where('inspection_type_id', $request->input('inspection_type_id'))
                    ->pluck('id')
                    ->toArray();
            } else {
                $inspection = $this->inspectionRepository->find($inspection_id);

                if ($vehicle->id == $inspection->vehicle_id) {

                    $form = new InspectionFormResource($inspection);

                    $inputs = $inspection->inspection_group_inspection
                        ->where('inspection_type_id', $request->input('inspection_type_id'))
                        ->pluck('id')
                        ->toArray();
                } else {
                    $inputs = $vehicle->inspection_group_vehicle
                        ->where('inspection_type_id', $request->input('inspection_type_id'))
                        ->pluck('id')
                        ->toArray();
                }
            }

            if (empty($inputs)) {
                $tabs = collect([]);
            } else {
                $tabs = $this->inspectionTypeGroupRepository->list(
                    [
                        'typeData'           => 'all',
                        'inspection_type_id' => $request->input('inspection_type_id'),
                        'ids'                => $inputs,
                        'sortBy'             => json_encode([
                            [
                                'key'   => 'order',
                                'order' => 'asc',
                            ],
                        ]),
                    ],
                    with: ['inspectionTypeInputs'],
                    select: ['id', 'name', 'order']
                );

                $newOrder = 1;
                foreach ($tabs as $tab) {
                    $tab->order = $newOrder;
                    $newOrder++;
                }
            }

            return [
                'code'    => 200,
                'vehicle' => $vehicle,
                'tabs'    => $tabs,
                'form'    => $form,
            ];
        });
    }

    public function excelExport(Request $request)
    {
        return $this->execute(function () use ($request) {
            $request['typeData'] = 'all';

            $data = $this->inspectionRepository->paginate($request->all());

            $excel = Excel::raw(new InspectionListExport($data), \Maatwebsite\Excel\Excel::XLSX);

            $excelBase64 = base64_encode($excel);
            return [
                'code' => 200,
                'excel' => $excelBase64
            ];
        });
    }

    public function pdfExport(Request $request)
    {
        return $this->execute(function () use ($request) {

            $inspection = $this->inspectionRepository->find($request->input('id'));
            $vehicle = $this->vehicleRepository->find($inspection->vehicle->id);

            $inpectionGroupsIds = $inspection->inspection_group_inspection->where('inspection_type_id', $inspection->inspection_type_id)->pluck('id');

            $tabs = InspectionTypeGroup::select(['id', 'name'])
                ->with([
                    'inspectionTypeInputs:id,inspection_type_group_id,name',
                    'inspectionTypeInputs.inspectionInputResponses:id,inspection_type_input_id,response,observation,inspection_id',
                    'inspectionTypeInputs.inspectionInputResponses' => function ($query) use ($inspection) {
                        $query->where('inspection_id', $inspection->id);
                    },
                ])
                ->whereIn('id', $inpectionGroupsIds)->get();

            $data = [
                'inspection_type_id' => $inspection['inspection_type_id'],
                'inspection_date' => Carbon::parse($inspection['inspection_date'])->format('d-m-Y'),
                'city' => ucfirst($inspection->city->name),
                'operator' => [
                    'name' => $inspection->user_operator->name,
                    'document' => $inspection->user_operator->document,
                    'license' => $inspection->user_operator->license,
                ],
                'vehicle' => [
                    'id' => $vehicle->id,
                    'license_plate' => $vehicle->license_plate,
                    'brand_vehicle_name' => $vehicle->brand_vehicle?->name,
                    'model' => $vehicle->model,
                    'vehicle_structure_name' => $vehicle->vehicle_structure?->name,
                ],
                'documents' => $vehicle->type_documents->map(function ($item) use ($inspection) {
                    $inspectionDocumentVerification = $inspection->inspectionDocumentVerifications->where('vehicle_document_id', $item->id)->first();
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
                'general_comment' => $inspection->general_comment,
                'getResponseTypeInspection' => getResponseTypeInspection($inspection['inspection_type_id']),
                'inspectionInputResponses' => $tabs->map(function ($item) use ($inspection) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'inspectionTypeInputs' => $item->inspectionTypeInputs->map(function ($input) use ($inspection) {
                            $getResponseTypeInspection = getResponseTypeInspection($inspection->inspection_type_id);

                            $responses = [];
                            $response = $input->inspectionInputResponses->first();

                            if ($inspection->inspection_type_id == 1 && isset($response->response['value'])) {
                                $decodedResponse = json_decode(json_encode($response->response), true);

                                $response['response'] = $decodedResponse['value'];
                            }


                            foreach ($getResponseTypeInspection as $key => $value) {
                                $responses[$key] = '';

                                $tempResponse = json_decode($response['response'], true)['value'] ?? $response['response'];

                                if ($value['value'] == $tempResponse) {
                                    $responses[$key] = 'X';
                                }
                            }

                            return [
                                'name' => $input->name,
                                'responses' => $responses,
                                'observation' => $response['observation'],
                            ];
                        }),
                    ];
                }),
            ];

            $pdf = $this->inspectionRepository->pdf('Exports.Inspection.InspectionExportPDF', $data);

            $pdfBase64 = base64_encode($pdf);
            return [
                'code' => 200,
                'pdf' => $pdfBase64
            ];
        });
    }

    private function sendNotificationGenerateInspection($company, $data)
    {
        // Enviar el correo usando el job de Brevo
        BrevoProcessSendEmail::dispatch(
            emailTo: [
                [
                    "name" => $company->name,
                    "email" => $company->email,
                ],
            ],
            subject: $data['title'],
            templateId: 7,  // El ID de la plantilla de Brevo que quieres usar
            params: [
                "full_name" => $company->full_name,
                "type_inspection" => $data['type_inspection'],
                "license_plate" => $data['license_plate'],
                "bussines_name" => $company->name,
                'action_url' => env("SYSTEM_URL_FRONT") . $data['action_url'],

            ],  // Aquí pasas los parámetros para la plantilla, por ejemplo, el texto del mensaje
        );
    }

    public function showReportInfo(Request $request, $id)
    {
        return $this->execute(function () use ($id) {

            $inspection = $this->inspectionRepository->find($id);

            $inputs = $inspection->inspection_group_inspection
                ->where('inspection_type_id', $inspection->inspection_type_id)
                ->pluck('id')
                ->toArray();

            if (empty($inputs)) {
                $tabs = collect([]);
            } else {
                $tabs = $this->inspectionTypeGroupRepository->list(
                    [
                        'typeData'           => 'all',
                        'inspection_type_id' => $inspection->inspection_type_id,
                        'ids'                => $inputs,
                        'sortBy'             => json_encode([
                            [
                                'key'   => 'order',
                                'order' => 'asc',
                            ],
                        ]),
                    ],
                    with: ['inspectionTypeInputs'],
                    select: ['id', 'name', 'order']
                );

                $newOrder = 1;
                foreach ($tabs as $tab) {
                    $tab->order = $newOrder;
                    $newOrder++;
                }
            }

            $info = [];
            foreach ($tabs as $tab) {
                $group = [
                    'tab_name' => $tab->name,
                    'inputs'   => []
                ];
                if (isset($tab['inspectionTypeInputs']) && count($tab['inspectionTypeInputs']) > 0) {
                    foreach ($tab['inspectionTypeInputs'] as $input) {
                        $inspectionInputResponse = $input['inspectionInputResponses']->first();

                        // Obtención del valor: para tipo 1 se decodifica si es necesario
                        if ($inspection->inspection_type_id == 1) {
                            $decodedResponse = json_decode($inspectionInputResponse->response, true);
                            $rawValue = $decodedResponse['value'] ?? $inspectionInputResponse->response;
                        } else {
                            $rawValue = $inspectionInputResponse->response;
                        }

                        // Filtrar según el tipo de inspección
                        $include = false;
                        if ($inspection->inspection_type_id == 1) {
                            if (in_array($rawValue, ['regular', 'bad'])) {
                                $include = true;
                            }
                        } elseif ($inspection->inspection_type_id == 2) {
                            if ($rawValue === 'does not comply') {
                                $include = true;
                            }
                        } else {
                            $include = true; // Por defecto, si se requiere otro comportamiento
                        }
                        if (!$include) {
                            continue;
                        }

                        // Traducción usando la función auxiliar
                        $translatedValue = $this->translateInspectionResponse($rawValue, $inspection->inspection_type_id);

                        $group['inputs'][] = [
                            'input_name'  => $input['name'],
                            'value'       => $translatedValue,
                        ];
                    }
                }
                $info[] = $group;
            }

            $expiredDocuments = $inspection->vehicle->type_documents
                ->where('expiration_date', '<', now());

            if ($expiredDocuments->isNotEmpty()) {
                $documentsTab = [
                    'tab_name' => 'Documentos',
                    'inputs'   => []
                ];

                foreach ($expiredDocuments as $document) {

                    $expiration = [
                        'title' => 'Vencido',
                        'color' => '#DC3545',
                    ];

                    $documentsTab['inputs'][] = [
                        'input_name'  => $document->type_document->name,
                        'value'       => $expiration,
                    ];
                }

                $info[] = $documentsTab;
            }

            return [
                'code' => 200,
                'info' => $info
            ];
        });
    }


    private function translateInspectionResponse($value, $inspectionTypeId)
    {
        if ($inspectionTypeId == 1) {
            $types = [
                ['value' => 'regular', 'title' => 'Regular', 'color' => '#FFC107'], // Amarillo
                ['value' => 'bad', 'title' => 'Malo', 'color' => '#DC3545'], // Rojo
            ];
        } elseif ($inspectionTypeId == 2) {
            $types = [
                ['value' => 'does not comply', 'title' => 'No Cumple', 'color' => '#DC3545'], // Rojo
            ];
        } else {
            $types = [];
        }

        $result = getStatus($value, $types, 'value', 'title', '===');

        // Buscar el color correspondiente en el array de tipos
        $color = collect($types)->firstWhere('value', $value)['color'] ?? '#6C757D'; // Color gris por defecto

        return [
            'title' => $result,
            'color' => $color
        ];
    }
}
