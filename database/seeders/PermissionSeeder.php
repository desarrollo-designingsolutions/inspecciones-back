<?php

namespace Database\Seeders;

use App\Helpers\Constants;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Datos para insertar o actualizar
        $arrayData = [
            [
                'id' => 1,
                'name' => 'menu.home',
                'description' => 'Visualizar Menú Inicio',
                'menu_id' => 1,
            ],
            [
                'id' => 2,
                'name' => 'company.list',
                'description' => 'Visualizar Módulo de Compañia',
                'menu_id' => 2,
            ],
            [
                'id' => 3,
                'name' => 'menu.user.father',
                'description' => 'Visualizar Menú Acceso de usuarios',
                'menu_id' => 3,
            ],
            [
                'id' => 4,
                'name' => 'menu.user',
                'description' => 'Visualizar Menú Usuarios',
                'menu_id' => 4,
            ],
            [
                'id' => 5,
                'name' => 'menu.role',
                'description' => 'Visualizar Menú Roles',
                'menu_id' => 5,
            ],
            [
                'id' => 6,
                'name' => 'client.list',
                'description' => 'Visualizar Menú Clientes',
                'menu_id' => 6,
            ],
            [
                'id' => 7,
                'name' => 'menu.configuration',
                'description' => 'Visualizar Menú Configuración',
                'menu_id' => 7,
            ],
            [
                'id' => 8,
                'name' => 'menu.type.vehicle',
                'description' => 'Visualizar Menú Tipo De Vehículos',
                'menu_id' => 8,
            ],
            [
                'id' => 9,
                'name' => 'menu.brand.vehicle',
                'description' => 'Visualizar Menú Marca De Vehículos',
                'menu_id' => 9,
            ],
            [
                'id' => 10,
                'name' => 'menu.type.document',
                'description' => 'Visualizar Menú Tipo De Documentos',
                'menu_id' => 10,
            ],
        ];

        // Inicializar la barra de progreso
        $this->command->info('Starting Seed Data ...');
        $bar = $this->command->getOutput()->createProgressBar(count($arrayData));

        // Insertar o actualizar permisos
        foreach ($arrayData as $value) {
            Permission::updateOrCreate(
                ['id' => $value['id']],
                [
                    'name' => $value['name'],
                    'description' => $value['description'],
                    'menu_id' => $value['menu_id'],
                    'guard_name' => 'api'
                ]
            );
        }


        // Obtener permisos
        $permissions = Permission::whereIn('id', collect($arrayData)->pluck('id'))->get();

        // Asignar permisos al rol
        $role = Role::find(Constants::ROLE_SUPERADMIN_UUID);
        if ($role) {
            $role->syncPermissions($permissions);
        }

        // Sincronizar roles con usuarios
        $users = User::get();
        foreach ($users as $user) {
            $role = Role::find($user->role_id);
            if ($role) {
                $user->syncRoles($role);
            }
        }

        $bar->finish(); // Finalizar la barra

    }
}
