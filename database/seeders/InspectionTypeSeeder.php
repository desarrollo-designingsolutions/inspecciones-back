<?php

namespace Database\Seeders;

use App\Models\InspectionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InspectionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InspectionType::insert([
            [
                'id' => 1,
                'name' => 'Pre-Operacional',
                'order' => 1,
            ],
            [
                'id' => 2,
                'name' => 'HSEQ',
                'order' => 2,
            ],
        ]);
    }
}
