<?php

namespace Database\Seeders;

use App\Models\UserTypeDocument;
use Illuminate\Database\Seeder;

class UserTypeDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arrayData = [
            [
                'id' => 1,
                'name' => 'CC',
            ],
            [
                'id' => 2,
                'name' => 'CE',
            ],
            [
                'id' => 3,
                'name' => 'Pasaporte',
            ],
        ];

        // Inicializar la barra de progreso
        $this->command->info('Starting Seed Data ...');
        $bar = $this->command->getOutput()->createProgressBar(count($arrayData));

        foreach ($arrayData as $key => $value) {
            $data = UserTypeDocument::find($value['id']);
            if (! $data) {
                $data = new UserTypeDocument;
            }
            $data->id = $value['id'];
            $data->name = $value['name'];
            $data->save();
        }

        $bar->finish(); // Finalizar la barra
    }
}
