<?php

namespace App\Http\Controllers;

use App\Exports\TypeVehicleListExport;
use App\Helpers\Constants;
use App\Http\Requests\TypeVehicle\TypeVehicleStoreRequest;
use App\Http\Resources\TypeVehicle\TypeVehicleFormResource;
use App\Http\Resources\TypeVehicle\TypeVehicleListResource;
use App\Http\Resources\TypeVehicle\TypeVehiclePaginateResource;
use App\Repositories\TypeVehicleRepository;
use App\Traits\HttpResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class TypeVehicleController extends Controller
{
    use HttpResponseTrait;

    public function __construct(
        protected TypeVehicleRepository $typeVehicleRepository,
    ) {}

    public function paginate(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $this->typeVehicleRepository->paginate($request->all());
            $tableData = TypeVehiclePaginateResource::collection($data);

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
            $data = $this->typeVehicleRepository->list($request->all());
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
            return response()->json([
                'code' => 200,
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

    public function store(TypeVehicleStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $typeVehicle = $this->typeVehicleRepository->store($request->all());

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Tipo de vehículo agregado correctamente', 'data' => $typeVehicle]);
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
            $typeVehicle = $this->typeVehicleRepository->find($id);
            $form = new TypeVehicleFormResource($typeVehicle);

            return response()->json([
                'code' => 200,
                'form' => $form,
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

    public function update(TypeVehicleStoreRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $post = $request->all();

            $typeVehicle = $this->typeVehicleRepository->store($post);

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Tipo de vehículo modificado correctamente', 'data' => $typeVehicle]);
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
            $typeVehicle = $this->typeVehicleRepository->find($id);
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

            $model = $this->typeVehicleRepository->changeState($request->input('id'), strval($request->input('value')), $request->input('field'));

            ($model->is_active == 1) ? $msg = 'habilitado(a)' : $msg = 'inhabilitado(a)';

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Tipo de vehículo '.$msg.' con éxito']);
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

            $data = $this->typeVehicleRepository->paginate($request->all());

            $excel = Excel::raw(new TypeVehicleListExport($data), \Maatwebsite\Excel\Excel::XLSX);

            $excelBase64 = base64_encode($excel);

            return [
                'code' => 200,
                'excel' => $excelBase64
            ];
        });
    }
}
