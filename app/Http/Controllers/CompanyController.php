<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\CompanyStoreRequest;
use App\Http\Resources\Company\CompanyFormResource;
use App\Http\Resources\Company\CompanyListResource;
use App\Repositories\CompanyRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class CompanyController extends Controller
{
    public function __construct(
        protected CompanyRepository $companyRepository,
        protected QueryController $queryController,
    ) {}


    public function list(Request $request)
    {
        try {
            $data = $this->companyRepository->list($request->all());
            $tableData = CompanyListResource::collection($data);

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
            $selectInfiniteCountries = $this->queryController->selectInfiniteCountries(request());

            $form['start_date'] = Carbon::now()->format('Y-m-d');

            return response()->json([
                'code' => 200,
                'form' => $form,
                ...$selectInfiniteCountries,
            ]);
        } catch (Throwable $th) {

            return response()->json(['code' => 500, $th->getMessage(), $th->getLine()]);
        }
    }

    public function store(CompanyStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $post = $request->except(['start_date']);

            $company = $this->companyRepository->store($post);

            if ($request->file('logo')) {
                $file = $request->file('logo');
                $ruta = 'companies/company_' . $company->id . $request->input('logo');
                $logo = $file->store($ruta, 'public');
                $company->logo = $logo;
                $company->save();
            }

            DB::commit();
            return response()->json(['code' => 200, 'message' => 'Compañia agregada correctamente']);
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
            $selectInfiniteCountries = $this->queryController->selectInfiniteCountries(request());

            $company = $this->companyRepository->find($id);
            $form = new CompanyFormResource($company);

            return response()->json([
                'code' => 200,
                'form' => $form,
                ...$selectInfiniteCountries,
            ]);
        } catch (Throwable $th) {

            return response()->json(['code' => 500, $th->getMessage(), $th->getLine()]);
        }
    }


    public function update(CompanyStoreRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $post = $request->except(['start_date']);

            $company = $this->companyRepository->store($post, $id);

            if ($request->file('logo')) {
                $file = $request->file('logo');
                $ruta = 'companies/company_' . $company->id . $request->input('logo');
                $logo = $file->store($ruta, 'public');
                $company->logo = $logo;
                $company->save();
            }

            DB::commit();
            return response()->json(['code' => 200, 'message' => 'Compañia modificada correctamente']);
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
            $company = $this->companyRepository->find($id);
            if ($company) {

                // Verificar si hay registros relacionados
                if (
                    $company->users()->exists()
                ) {
                    throw new \Exception('No se puede eliminar la compañía, por que tiene relación de datos en otros módulos');
                }


                $company->delete();
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


            $model = $this->companyRepository->changeState($request->input('id'), strval($request->input('value')), $request->input('field'));

            ($model->is_active == 1) ? $msg = 'habilitada' : $msg = 'inhabilitada';

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Compañia ' . $msg . ' con éxito']);
        } catch (Throwable $th) {
            DB::rollback();

            return response()->json(['code' => 500, 'message' => $th->getMessage()]);
        }
    }
}
