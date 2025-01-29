<?php

namespace App\Http\Controllers;

use App\Exports\MaintenanceListExport;
use App\Http\Requests\Maintenance\MaintenanceStoreRequest;
use App\Http\Resources\Maintenance\MaintenanceFormResource;
use App\Http\Resources\Maintenance\MaintenanceListResource;
use App\Repositories\MaintenanceRepository;
use App\Traits\HttpTrait;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MaintenanceController extends Controller
{
    use HttpTrait;

    public function __construct(
        protected MaintenanceRepository $maintenanceRepository,
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

    public function create()
    {
        return $this->execute(function () {

            return [
                'code' => 200,
            ];
        });
    }

    public function store(MaintenanceStoreRequest $request)
    {
        return $this->runTransaction(function () use ($request) {
            $post = $request->all();

            $maintenance = $this->maintenanceRepository->store($post);

            return ['code' => 200, 'message' => 'Vehiculo agregado correctamente', 'data' => $maintenance];
        });
    }

    public function edit($id)
    {
        return $this->execute(function () use ($id) {
            $maintenance = $this->maintenanceRepository->find($id);
            $form = new MaintenanceFormResource($maintenance);

            return [
                'code' => 200,
                'form' => $form,
            ];
        });
    }

    public function update(MaintenanceStoreRequest $request, $id)
    {
        return $this->runTransaction(function () use ($request) {
            $post = $request->all();

            $maintenance = $this->maintenanceRepository->store($post);

            return ['code' => 200, 'message' => 'Vehiculo modificado correctamente', 'data' => $maintenance];
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

    public function changeStatus(Request $request)
    {
        return $this->runTransaction(function () use ($request) {
            $model = $this->maintenanceRepository->changeState($request->input('id'), strval($request->input('value')), $request->input('field'));

            ($model->is_active == 1) ? $msg = 'habilitado(a)' : $msg = 'inhabilitado(a)';

            return ['code' => 200, 'message' => 'Vehiculo '.$msg.' con éxito'];
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
