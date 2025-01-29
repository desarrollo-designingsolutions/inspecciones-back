<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arrayData = [
            [
                'id' => 1,
                'order' => 10,
                'title' => 'Inicio',
                'to' => 'Home',
                'icon' => 'tabler-home',
                'requiredPermission' => 'menu.home',
            ],
            [
                'id' => 2,
                'order' => 20,
                'title' => 'Compañias',
                'to' => 'Company-List',
                'icon' => ' tabler-building',
                'requiredPermission' => 'company.list',
            ],
            [
                'id' => 3,
                'order' => 30,
                'title' => 'Usuarios',
                'icon' => 'tabler-user-shield',
                'requiredPermission' => 'menu.user.father',
            ],
            [
                'id' => 4,
                'order' => 40,
                'title' => 'Usuarios',
                'to' => 'User-List',
                'icon' => '',
                'father' => 3,
                'requiredPermission' => 'menu.user',
            ],
            [
                'id' => 5,
                'order' => 50,
                'title' => 'Roles',
                'to' => 'Role-List',
                'icon' => '',
                'father' => 3,
                'requiredPermission' => 'menu.role',
            ],
            [
                'id' => 6,
                'order' => 60,
                'title' => 'Clientes',
                'to' => 'Client-List',
                'icon' => 'tabler-users-group',
                'requiredPermission' => 'client.list',
            ],
            [
                'id' => 7,
                'order' => 9999,
                'title' => 'Configuración',
                'to' => null,
                'icon' => 'tabler-settings',
                'father' => null,
                'requiredPermission' => 'menu.configuration',
            ],
            [
                'id' => 8,
                'order' => 80,
                'title' => 'Clase de vehículos',
                'to' => 'Type-Vehicle-List',
                'icon' => '',
                'father' => 7,
                'requiredPermission' => 'menu.type.vehicle',
            ],
            [
                'id' => 9,
                'order' => 90,
                'title' => 'Marcas de vehículo',
                'to' => 'Brand-Vehicle-List',
                'icon' => '',
                'father' => 7,
                'requiredPermission' => 'menu.brand.vehicle',
            ],
            [
                'id' => 10,
                'order' => 100,
                'title' => 'Tipo de documentos',
                'to' => 'Type-Document-List',
                'icon' => '',
                'father' => 7,
                'requiredPermission' => 'menu.type.document',
            ],
            [
                'id' => 11,
                'order' => 110,
                'title' => 'Elementos de emergencia',
                'to' => 'Emergency-Element-List',
                'icon' => '',
                'father' => 7,
                'requiredPermission' => 'menu.emergency.element',
            ],
            [
                'id' => 12,
                'order' => 120,
                'title' => 'Vehiculo',
                'to' => 'Vehicle-List',
                'icon' => 'tabler-car',
                'father' => null,
                'requiredPermission' => 'vehicle.list',
            ],
            [
                'id' => 13,
                'order' => 130,
                'title' => 'Inspecciones',
                'to' => 'Inspection-List',
                'icon' => 'tabler-list-check',
                'father' => null,
                'requiredPermission' => 'inspection.list',
            ],
            [
                'id' => 14,
                'order' => 140,
                'title' => 'Mantenimiento',
                'to' => 'Maintenance-List',
                'icon' => 'tabler-list-check',
                'father' => null,
                'requiredPermission' => 'maintenance.list',
            ],
        ];

        // Inicializar la barra de progreso
        $this->command->info('Starting Seed Data ...');
        $bar = $this->command->getOutput()->createProgressBar(count($arrayData));

        foreach ($arrayData as $key => $value) {
            $data = Menu::find($value['id']);
            if (! $data) {
                $data = new Menu;
            }
            $data->id = $value['id'];
            $data->order = $value['order'];
            $data->title = $value['title'];
            $data->to = $value['to'] ?? null;
            $data->icon = $value['icon'];
            $data->father = $value['father'] ?? null;
            $data->requiredPermission = $value['requiredPermission'];
            $data->save();
        }

        $bar->finish(); // Finalizar la barra
    }
}
