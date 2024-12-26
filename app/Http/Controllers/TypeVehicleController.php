<?php

namespace App\Http\Controllers;

use App\Exports\TypeVehicleListExport;
use App\Http\Requests\TypeVehicle\TypeVehicleStoreRequest;
use App\Http\Resources\TypeVehicle\TypeVehicleFormResource;
use App\Http\Resources\TypeVehicle\TypeVehicleListResource;
use App\Repositories\TypeVehicleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Maatwebsite\Excel\Facades\Excel;

class TypeVehicleController extends Controller
{
    public function __construct(
        protected TypeVehicleRepository $TypeVehicleRepository,
        protected QueryController $queryController,
    ) {}

    public function list(Request $request)
    {
        try {
            $data = $this->TypeVehicleRepository->list($request->all());
            $tableData = TypeVehicleListResource::collection($data);

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

    public function store(TypeVehicleStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $typeVehicle = $this->TypeVehicleRepository->store($request->all());

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Tipo de vehículo agregado correctamente', 'data' => $typeVehicle]);
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
            $typeVehicle = $this->TypeVehicleRepository->find($id);
            $form = new TypeVehicleFormResource($typeVehicle);

            return response()->json([
                'code' => 200,
                'form' => $form,
            ]);
        } catch (Throwable $th) {

            return response()->json(['code' => 500, $th->getMessage(), $th->getLine()]);
        }
    }

    public function update(TypeVehicleStoreRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $post = $request->all();

            $typeVehicle = $this->TypeVehicleRepository->store($post);

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Tipo de vehículo modificado correctamente', 'data' => $typeVehicle]);
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
            $typeVehicle = $this->TypeVehicleRepository->find($id);
            if ($typeVehicle) {
                $typeVehicle->delete();
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

            $model = $this->TypeVehicleRepository->changeState($request->input('id'), strval($request->input('value')), $request->input('field'));

            ($model->is_active == 1) ? $msg = 'habilitado(a)' : $msg = 'inhabilitado(a)';

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'typeVehiclee '.$msg.' con éxito']);
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

             $data = $this->TypeVehicleRepository->list([
                ...$filter,
                ...$request->all(),
            ]);

            $excel = Excel::raw(new TypeVehicleListExport($data), \Maatwebsite\Excel\Excel::XLSX);

            $excelBase64 = base64_encode($excel);

            return response()->json(['code' => 200, 'excel' => $excelBase64]);
        } catch (Throwable $th) {
            return response()->json(['code' => 500, 'message' => $th->getMessage()]);
        }
    }
}
