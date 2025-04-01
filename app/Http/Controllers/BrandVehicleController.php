<?php

namespace App\Http\Controllers;

use App\Exports\BrandVehicleListExport;
use App\Helpers\Constants;
use App\Http\Requests\BrandVehicle\BrandVehicleStoreRequest;
use App\Http\Resources\BrandVehicle\BrandVehicleFormResource;
use App\Http\Resources\BrandVehicle\BrandVehicleListResource;
use App\Http\Resources\BrandVehicle\BrandVehiclePaginateResource;
use App\Repositories\BrandVehicleRepository;
use App\Traits\HttpResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class BrandVehicleController extends Controller
{
    use HttpResponseTrait;

    public function __construct(
        protected BrandVehicleRepository $brandVehicleRepository,
    ) {}

    public function paginate(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $this->brandVehicleRepository->paginate($request->all());
            $tableData = BrandVehiclePaginateResource::collection($data);

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

    public function store(BrandVehicleStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $brandVehicle = $this->brandVehicleRepository->store($request->all());

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Marca de vehículo agregada correctamente', 'data' => $brandVehicle]);
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
            $brandVehicle = $this->brandVehicleRepository->find($id);
            $form = new BrandVehicleFormResource($brandVehicle);

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

    public function update(BrandVehicleStoreRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $post = $request->all();

            $brandVehicle = $this->brandVehicleRepository->store($post);

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Marca de vehículo modificada correctamente', 'data' => $brandVehicle]);
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

            $model = $this->brandVehicleRepository->changeState($request->input('id'), strval($request->input('value')), $request->input('field'));

            ($model->is_active == 1) ? $msg = 'habilitado(a)' : $msg = 'inhabilitado(a)';

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Marca de vehículo '.$msg.' con éxito']);
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

            $data = $this->brandVehicleRepository->paginate($request->all());

            $excel = Excel::raw(new BrandVehicleListExport($data), \Maatwebsite\Excel\Excel::XLSX);

            $excelBase64 = base64_encode($excel);

            return [
                'code' => 200,
                'excel' => $excelBase64
            ];
        });
    }
}
