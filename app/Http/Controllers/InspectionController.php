<?php

namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Http\Requests\Inspection\InspectionStoreRequest;
use App\Http\Resources\Inspection\InspectionFormResource;
use App\Http\Resources\Inspection\InspectionListResource;
use App\Repositories\InspectionRepository;
use App\Traits\HttpTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class InspectionController extends Controller
{
    use HttpTrait;

    public function __construct(
        protected InspectionRepository $inspectionRepository,
        protected QueryController $queryController,
    ) {}

    public function list(Request $request)
    {
        $this->execute(function () use ($request) {

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

    public function create()
    {
        $this->execute(function () {

            $selectStates = $this->queryController->selectStates(Constants::COUNTRY_ID);

            return [
                'code' => 200,
                ...$selectStates,
            ];
        });
    }

    public function store(InspectionStoreRequest $request)
    {
        $this->runTransaction(function () use ($request) {

            $post = $request->all();

            $inspection = $this->inspectionRepository->store($post);

            return [
                'code' => 200,
                'message' => 'Inspección agregado correctamente',
                'data' => $inspection
            ];
        });
    }

    public function edit($id)
    {
        $this->execute(function () use ($id) {

            $inspection = $this->inspectionRepository->find($id);

            $form = new InspectionFormResource($inspection);

            return [
                'code' => 200,
                'form' => $form,
            ];
        });
    }

    public function update(InspectionStoreRequest $request, $id)
    {
        $this->runTransaction(function () use ($request, $id) {

            $post = $request->all();

            $inspection = $this->inspectionRepository->store($post, $id);

            return [
                'code' => 200,
                'message' => 'Inspección modificado correctamente',
                'data' => $inspection
            ];
        });
    }

    public function delete($id)
    {
        $this->runTransaction(function () use ($id) {
            $inspection = $this->inspectionRepository->find($id);

            if ($inspection) {
                $inspection->delete();
                $msg = 'Registro eliminado correctamente';
            } else {
                $msg = 'El registro no existe';
            }
            return response()->json(
                ['code' => 200, 'message' => $msg
            ]);
        });
    }
}
