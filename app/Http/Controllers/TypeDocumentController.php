<?php

namespace App\Http\Controllers;

use App\Exports\TypeDocumentListExport;
use App\Helpers\Constants;
use App\Http\Requests\TypeDocument\TypeDocumentStoreRequest;
use App\Http\Resources\TypeDocument\TypeDocumentFormResource;
use App\Http\Resources\TypeDocument\TypeDocumentListResource;
use App\Http\Resources\TypeDocument\TypeDocumentPaginateResource;
use App\Repositories\TypeDocumentRepository;
use App\Traits\HttpResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class TypeDocumentController extends Controller
{
    use HttpResponseTrait;

    public function __construct(
        protected TypeDocumentRepository $typeDocumentRepository,
    ) {}

    public function paginate(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $this->typeDocumentRepository->paginate($request->all());
            $tableData = TypeDocumentPaginateResource::collection($data);

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
            $data = $this->typeDocumentRepository->list($request->all());
            $tableData = TypeDocumentListResource::collection($data);

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

    public function store(TypeDocumentStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $typeVehicle = $this->typeDocumentRepository->store($request->all());

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Tipo de documento agregado correctamente', 'data' => $typeVehicle]);
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
            $typeVehicle = $this->typeDocumentRepository->find($id);
            $form = new TypeDocumentFormResource($typeVehicle);

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

    public function update(TypeDocumentStoreRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $post = $request->all();

            $typeVehicle = $this->typeDocumentRepository->store($post);

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Tipo de documento modificado correctamente', 'data' => $typeVehicle]);
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
            $typeVehicle = $this->typeDocumentRepository->find($id);
            if ($typeVehicle) {

                if (
                    $typeVehicle->vehicleDocuments()->exists()
                ) {
                    throw new \Exception('No se puede eliminar el registro, por que tiene relación de datos en otros módulos');
                }

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

            $model = $this->typeDocumentRepository->changeState($request->input('id'), strval($request->input('value')), $request->input('field'));

            ($model->is_active == 1) ? $msg = 'habilitado(a)' : $msg = 'inhabilitado(a)';

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Tipo de documento '.$msg.' con éxito']);
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

            $data = $this->typeDocumentRepository->paginate($request->all());

            $excel = Excel::raw(new TypeDocumentListExport($data), \Maatwebsite\Excel\Excel::XLSX);

            $excelBase64 = base64_encode($excel);

            return [
                'code' => 200,
                'excel' => $excelBase64,
            ];
        });
    }
}
