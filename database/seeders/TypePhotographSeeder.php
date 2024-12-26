<?php

namespace Database\Seeders;

use App\Models\TypePhotograph;
use Illuminate\Database\Seeder;

class TypePhotographSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Datos para insertar o actualizar
        $arrayData = [
            [
                'name' => 'Frontal',
            ],
            [
                'name' => 'Trasera',
            ],
            [
                'name' => 'Lateral derecho',
            ],
            [
                'name' => 'Lateral izquierdo',
            ],
        ];

        // Inicializar la barra de progreso
        $this->command->info('Starting Seed Data ...');
        $bar = $this->command->getOutput()->createProgressBar(count($arrayData));

        // Insertar o actualizar estructuras de vehiculos
        foreach ($arrayData as $value) {
            TypePhotograph::updateOrCreate(
                ['name' => $value['name']],
                [
                    'name' => $value['name'],
                ]
            );
        }
        $bar->finish();
    }
}
