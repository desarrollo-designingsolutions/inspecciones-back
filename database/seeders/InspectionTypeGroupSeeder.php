<?php

namespace Database\Seeders;

use App\Models\InspectionTypeGroup;
use Illuminate\Database\Seeder;

class InspectionTypeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InspectionTypeGroup::insert([
            [
                'id' => 1,
                'inspection_type_id' => 1,
                'name' => 'Inspección Cabezote',
                'order' => 10,
            ],
            [
                'id' => 2,
                'inspection_type_id' => 1,
                'name' => 'Inspección Trailer',
                'order' => 20,
            ],
            [
                'id' => 3,
                'inspection_type_id' => 1,
                'name' => 'Inspección Bomba/Unidad de vacío',
                'order' => 30,
            ],
            [
                'id' => 4,
                'inspection_type_id' => 1,
                'name' => 'HSE',
                'order' => 40,
            ],
            [
                'id' => 5,
                'inspection_type_id' => 2,
                'name' => 'Cabezote',
                'order' => 50,
            ],
            [
                'id' => 6,
                'inspection_type_id' => 2,
                'name' => 'Tanque',
                'order' => 60,
            ],
            [
                'id' => 7,
                'inspection_type_id' => 2,
                'name' => 'Bomba de Vacio/Triplex',
                'order' => 70,
            ],
            [
                'id' => 8,
                'inspection_type_id' => 2,
                'name' => 'Elementos de Protección Personal',
                'order' => 80,
            ],
            [
                'id' => 9,
                'inspection_type_id' => 2,
                'name' => 'Elementos del Botiquin Vigentes',
                'order' => 90,
            ],
            [
                'id' => 10,
                'inspection_type_id' => 2,
                'name' => 'Extintor Derecho del Tanque',
                'order' => 100,
            ],
            [
                'id' => 11,
                'inspection_type_id' => 2,
                'name' => 'Extintor Izquierdo del Tanque',
                'order' => 110,
            ],
            [
                'id' => 12,
                'inspection_type_id' => 2,
                'name' => 'Kit de Derrame',
                'order' => 120,
            ],
        ]);
    }
}
