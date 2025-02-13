<?php

namespace App\Http\Controllers;

use App\Exports\ClientListExport;
use App\Helpers\Constants;
use App\Http\Requests\Client\ClientStoreRequest;
use App\Http\Resources\Client\ClientFormResource;
use App\Http\Resources\Client\ClientListResource;
use App\Repositories\ClientRepository;
use App\Traits\HttpTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ClientController extends Controller
{
    use HttpTrait;
    public function __construct(
        protected ClientRepository $clientRepository,
    ) {}

    public function list(Request $request)
    {
        try {
            $data = $this->clientRepository->list($request->all());
            $tableData = ClientListResource::collection($data);

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

    public function store(ClientStoreRequest $request)
    {
        return $this->runTransaction(function () use ($request) {

            $client = $this->clientRepository->store($request->all());

            return ['code' => 200, 'message' => 'Cliente agregado correctamente', 'data' => $client];
        });
    }

    public function edit($id)
    {
        try {
            $client = $this->clientRepository->find($id);
            $form = new ClientFormResource($client);

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

    public function update(ClientStoreRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $post = $request->all();

            $client = $this->clientRepository->store($post);

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Cliente modificado correctamente', 'data' => $client]);
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
            $client = $this->clientRepository->find($id);
            if ($client) {
                $client->delete();
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

            $model = $this->clientRepository->changeState($request->input('id'), strval($request->input('value')), $request->input('field'));

            ($model->is_active == 1) ? $msg = 'habilitado(a)' : $msg = 'inhabilitado(a)';

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Cliente ' . $msg . ' con éxito']);
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
        try {

            $filter = [
                'typeData' => 'all',
            ];

            $data = $this->clientRepository->list([
                ...$filter,
                ...$request->all(),
            ]);

            $excel = Excel::raw(new ClientListExport($data), \Maatwebsite\Excel\Excel::XLSX);

            $excelBase64 = base64_encode($excel);

            return response()->json(['code' => 200, 'excel' => $excelBase64]);
        } catch (Throwable $th) {
            return response()->json([
                'code' => 500,
                'message' => Constants::ERROR_MESSAGE_TRYCATCH,
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
    }
}
