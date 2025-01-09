<?php

namespace Database\Seeders;

use App\Models\TypeLicense;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeLicenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arrayData = [
            [
                'id' => 1,
                'name' => 'C1',
            ],
            [
                'id' => 2,
                'name' => 'C2',
            ],
        ];

        // Inicializar la barra de progreso
        $this->command->info('Starting Seed Data ...');
        $bar = $this->command->getOutput()->createProgressBar(count($arrayData));

        foreach ($arrayData as $key => $value) {
            $data = TypeLicense::find($value['id']);
            if (!$data) {
                $data = new TypeLicense();
            }
            $data->id = $value['id'];
            $data->name = $value['name'];
            $data->save();
        }

        $bar->finish(); // Finalizar la barra
    }
}
