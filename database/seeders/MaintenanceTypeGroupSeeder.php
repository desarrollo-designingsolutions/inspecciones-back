<?php

namespace Database\Seeders;

use App\Models\MaintenanceTypeGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaintenanceTypeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MaintenanceTypeGroup::insert([
            [
                'id' => 1,
                'name' => 'Sistema de motor',
                'order' => 1,
                'maintenance_type_id' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Sistema de dirección',
                'order' => 2,
                'maintenance_type_id' => 1,
            ],
            [
                'id' => 3,
                'name' => 'Sistema de tracción y embrague',
                'order' => 3,
                'maintenance_type_id' => 1,
            ],
            [
                'id' => 4,
                'name' => 'Sistema de suspensión',
                'order' => 4,
                'maintenance_type_id' => 1,
            ],
            [
                'id' => 5,
                'name' => 'Sistema eléctrico y de encendido',
                'order' => 5,
                'maintenance_type_id' => 1,
            ],
            [
                'id' => 6,
                'name' => 'Sistema de vacío (Camión de vacío)',
                'order' => 6,
                'maintenance_type_id' => 1,
            ],
            [
                'id' => 7,
                'name' => 'Tanque y/o trailer',
                'order' => 7,
                'maintenance_type_id' => 1,
            ],
            [
                'id' => 8,
                'name' => 'Sistema de frenos',
                'order' => 8,
                'maintenance_type_id' => 1,
            ],
            [
                'id' => 9,
                'name' => 'Sistema unidad de vacío (Mula vacío)',
                'order' => 9,
                'maintenance_type_id' => 1,
            ],
            [
                'id' => 10,
                'name' => 'Otros mantenimientos',
                'order' => 10,
                'maintenance_type_id' => 1,
            ],
        ]);
    }
}
