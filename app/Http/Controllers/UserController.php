<?php

namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Resources\User\UserFormResource;
use App\Http\Resources\User\UserListResource;
use App\Repositories\CompanyRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserController extends Controller
{
    public function __construct(
        protected UserRepository $userRepository,
        protected RoleRepository $roleRepository,
        protected CompanyRepository $companyRepository,
    ) {}

    public function list(Request $request)
    {
        try {
            $data = $this->userRepository->list($request->all());
            $tableData = UserListResource::collection($data);

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

            $roles = $this->roleRepository->selectList(request());
            $companies = $this->companyRepository->selectList();

            return response()->json([
                'code' => 200,
                'roles' => $roles,
                'companies' => $companies,
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

    public function store(UserStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $post = $request->except(['confirmedPassword']);

            $data = $this->userRepository->store($post, withCompany: false);

            $data->syncRoles($request->input('role_id'));

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Usuario agregado correctamente']);
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
            $roles = $this->roleRepository->selectList(request());
            $companies = $this->companyRepository->selectList();

            $user = $this->userRepository->find($id);
            $form = new UserFormResource($user);

            return response()->json([
                'code' => 200,
                'form' => $form,
                'roles' => $roles,
                'companies' => $companies,
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

    public function update(UserStoreRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $post = $request->except(['confirmedPassword']);

            $data = $this->userRepository->store($post, $id, withCompany: false);

            $data->syncRoles($request->input('role_id'));

            DB::commit();

            clearCacheLaravel();

            return response()->json(['code' => 200, 'message' => 'Usuario modificado correctamente']);
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
            $user = $this->userRepository->find($id);
            if ($user) {

                // Verificar si hay registros relacionados
                // if (
                //     $user->users()->exists()
                // ) {
                //     throw new \Exception('No se puede eliminar el usuario, por que tiene relación de datos en otros módulos');
                // }

                $user->delete();
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

            $model = $this->userRepository->changeState($request->input('id'), strval($request->input('value')), $request->input('field'));

            ($model->is_active == 1) ? $msg = 'habilitada' : $msg = 'inhabilitada';

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'User '.$msg.' con éxito']);
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

    public function changePassword(Request $request)
    {
        try {
            DB::beginTransaction();
            // Obtener el usuario autenticado
            $user = $this->userRepository->find($request->input('id'));

            // Cambiar la contraseña
            $user->password = $request->input('new_password');
            $user->save();

            DB::commit();

            return response()->json(['code' => 200, 'message' => 'Contraseña modificada con éxito.']);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'code' => 500,
                'message' => Constants::ERROR_MESSAGE_TRYCATCH,
                'error' => $th->getMessage(),
                'line' => $th->getLine(),
            ], 500);
        }
    }
}
