<?php

namespace Database\Seeders;

use App\Models\VehicleStructure;
use Illuminate\Database\Seeder;

class VehicleStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Datos para insertar o actualizar
        $arrayData = [
            [
                'name' => 'Rigido',
            ],
            [
                'name' => 'Articulado',
            ],
            [
                'name' => 'Volqueta',
            ],
            [
                'name' => 'Camioneta',
            ],
        ];

        // Inicializar la barra de progreso
        $this->command->info('Starting Seed Data ...');
        $bar = $this->command->getOutput()->createProgressBar(count($arrayData));

        // Insertar o actualizar estructuras de vehículos
        foreach ($arrayData as $value) {
            VehicleStructure::updateOrCreate(
                ['name' => $value['name']],
                [
                    'name' => $value['name'],
                ]
            );
        }
        $bar->finish();
    }
}
