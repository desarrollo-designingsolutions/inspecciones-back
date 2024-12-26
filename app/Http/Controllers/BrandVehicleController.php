<?php

namespace App\Http\Controllers;

use App\Exports\BrandVehicleListExport;
use App\Http\Requests\BrandVehicle\BrandVehicleStoreRequest;
use App\Http\Resources\BrandVehicle\BrandVehicleFormResource;
use App\Http\Resources\BrandVehicle\BrandVehicleListResource;
use App\Repositories\BrandVehicleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Maatwebsite\Excel\Facades\Excel;

class BrandVehicleController extends Controller
{
    public function __construct(
        protected BrandVehicleRepository $brandVehicleRepository,
        protected QueryController $queryController,
    ) {}

    public function list(Request $request)
    {
        try {
            $data = $this->brandVehicleRepository->list($request->all());
            $tableData = BrandVehicleListResource::collection($data);

            return [
                'code' => 200,
                'tableData' => $tableData,
                'lastPage' => $data->lastPage(),
                'totalData' => $data->total(),
                'totalPage' => $data->perPage(),
                'currentPage' => $data->currentPage(),
            ];
        } catch (Throwable $th) {

            return response()->json(['code' => 500, 'message' => 'Error Al Buscar Los Datos', $th->getMessage(), $th->getLine()]);
        }
    }

    public function create()
    {
        try {
            return response()->json([
                'code' => 200,
            ]);
        } catch (Throwable $th) {

            return response()->json(['code' => 500, $th->getMessage(), $th->getLine()]);
        }
    }

    public function store(BrandVehicleStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $brandVehicle = $this->brandVehicleRepository->store($request->all());

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Tipo de vehículo agregado correctamente', 'data' => $brandVehicle]);
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'code' => 500,
                'message' => 'Algo Ocurrio, Comunicate Con El Equipo De Desarrollo',
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $brandVehicle = $this->brandVehicleRepository->find($id);
            $form = new BrandVehicleFormResource($brandVehicle);

            return response()->json([
                'code' => 200,
                'form' => $form,
            ]);
        } catch (Throwable $th) {

            return response()->json(['code' => 500, $th->getMessage(), $th->getLine()]);
        }
    }

    public function update(BrandVehicleStoreRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $post = $request->all();

            $brandVehicle = $this->brandVehicleRepository->store($post);

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Tipo de vehículo modificado correctamente', 'data' => $brandVehicle]);
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'code' => 500,
                'message' => 'Algo Ocurrio, Comunicate Con El Equipo De Desarrollo',
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $brandVehicle = $this->brandVehicleRepository->find($id);
            if ($brandVehicle) {
                $brandVehicle->delete();
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

            $model = $this->brandVehicleRepository->changeState($request->input('id'), strval($request->input('value')), $request->input('field'));

            ($model->is_active == 1) ? $msg = 'habilitado(a)' : $msg = 'inhabilitado(a)';

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'brandVehiclee '.$msg.' con éxito']);
        } catch (Throwable $th) {
            DB::rollback();

            return response()->json(['code' => 500, 'message' => $th->getMessage()]);
        }
    }

    public function excelExport(Request $request)
    {
        try {

            $filter = [
                'typeData' => 'all',
            ];

             $data = $this->brandVehicleRepository->list([
                ...$filter,
                ...$request->all(),
            ]);

            $excel = Excel::raw(new BrandVehicleListExport($data), \Maatwebsite\Excel\Excel::XLSX);

            $excelBase64 = base64_encode($excel);

            return response()->json(['code' => 200, 'excel' => $excelBase64]);
        } catch (Throwable $th) {
            return response()->json(['code' => 500, 'message' => $th->getMessage()]);
        }
    }
}
