<?php

namespace Database\Seeders;

use App\Models\MaintenanceType;
use Illuminate\Database\Seeder;

class MaintenanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MaintenanceType::insert([
            [
                'id' => 1,
                'name' => 'Normal',
                'order' => 10,
            ],
        ]);
    }
}
