<?php

namespace Database\Seeders;

use App\Models\MaintenanceTypeInput;
use Illuminate\Database\Seeder;

class MaintenanceTypeInputSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arrayData = [
            [
                'maintenance_type_group_id' => 1,
                'name' => 'Cambio de aceite motor',
            ],
            [
                'maintenance_type_group_id' => 1,
                'name' => 'Cambio filtro de aceite, de combustible y filtro de agua',
            ],
            [
                'maintenance_type_group_id' => 1,
                'name' => 'Cambio filtro de aire',
            ],
            [
                'maintenance_type_group_id' => 1,
                'name' => 'Cambio de valvulina',
            ],
            [
                'maintenance_type_group_id' => 1,
                'name' => 'Engrase general del vehículo',
            ],
            [
                'maintenance_type_group_id' => 1,
                'name' => 'Cambio valvulina',
            ],
            [
                'maintenance_type_group_id' => 1,
                'name' => 'Cambio de Refrigerante',
            ],
            [
                'maintenance_type_group_id' => 1,
                'name' => 'Cambio de barillas de medición de niveles',
            ],
            [
                'maintenance_type_group_id' => 1,
                'name' => 'Radiador (Tanque de agua)',
            ],
            [
                'maintenance_type_group_id' => 1,
                'name' => 'Cambio de sensores',
            ],
            [
                'maintenance_type_group_id' => 1,
                'name' => 'Inyectores',
            ],
            [
                'maintenance_type_group_id' => 1,
                'name' => 'Motor',
            ],
            [
                'maintenance_type_group_id' => 2,
                'name' => 'Caja de la dirección',
            ],
            [
                'maintenance_type_group_id' => 2,
                'name' => 'Retenedores de aceite hidráulico',
            ],
            [
                'maintenance_type_group_id' => 2,
                'name' => 'Crucetas',
            ],
            [
                'maintenance_type_group_id' => 2,
                'name' => 'Mangueras',
            ],
            [
                'maintenance_type_group_id' => 2,
                'name' => 'Bomba hidráulica',
            ],
            [
                'maintenance_type_group_id' => 2,
                'name' => 'Barra estabilizadora',
            ],
            [
                'maintenance_type_group_id' => 2,
                'name' => 'Alineación',
            ],
            [
                'maintenance_type_group_id' => 2,
                'name' => 'Spínder',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Disco del cloutch (embrague)',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Prensa del cloutch (embrague)',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Balinera del embrague',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Niveles de aceite de la transmisión (caja)',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Niveles de aceite de los diferenciales',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Yoke del cardan primario y secundario',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Crucetas del cardan primario y secundario',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Engrases del sistema (lubricación)',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Diferenciales (corona, spig, planetarios)',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Ejes de diferenciales',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Retenedores de los ejes',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Rodamientos',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Tandem',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Pernos',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Llantas de repuesto',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Llantas delanteras',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Llantas traseras',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Cambio de rodajas',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Cambio de espárragos',
            ],
            [
                'maintenance_type_group_id' => 3,
                'name' => 'Troque',
            ],
            [
                'maintenance_type_group_id' => 4,
                'name' => 'Muelles delanteros y traseros',
            ],
            [
                'maintenance_type_group_id' => 4,
                'name' => 'Bombonas traseras y delanteras',
            ],
            [
                'maintenance_type_group_id' => 4,
                'name' => 'Tornillos centrales de muelle',
            ],
            [
                'maintenance_type_group_id' => 4,
                'name' => 'Grapas de muelles',
            ],
            [
                'maintenance_type_group_id' => 4,
                'name' => 'Tensores traseros y delanteros',
            ],
            [
                'maintenance_type_group_id' => 4,
                'name' => 'Balancines',
            ],
            [
                'maintenance_type_group_id' => 4,
                'name' => 'Pasadores de balancines y tensores',
            ],
            [
                'maintenance_type_group_id' => 4,
                'name' => 'Pasadores de viga',
            ],
            [
                'maintenance_type_group_id' => 4,
                'name' => 'Bujes',
            ],
            [
                'maintenance_type_group_id' => 4,
                'name' => 'Bandas',
            ],
            [
                'maintenance_type_group_id' => 4,
                'name' => 'Corbatines',
            ],
            [
                'maintenance_type_group_id' => 5,
                'name' => 'Sistema de baterías',
            ],
            [
                'maintenance_type_group_id' => 5,
                'name' => 'Revisión sistema eléctrico general (incluye luces)',
            ],
            [
                'maintenance_type_group_id' => 5,
                'name' => 'Alternador',
            ],
            [
                'maintenance_type_group_id' => 5,
                'name' => 'Arranque',
            ],
            [
                'maintenance_type_group_id' => 5,
                'name' => 'Cables',
            ],
            [
                'maintenance_type_group_id' => 6,
                'name' => 'Cambio correas de la bomba de vacío',
            ],
            [
                'maintenance_type_group_id' => 6,
                'name' => 'Cambio aceite de la bomba de vacío',
            ],
            [
                'maintenance_type_group_id' => 6,
                'name' => 'Cambio de filtro',
            ],
            [
                'maintenance_type_group_id' => 6,
                'name' => 'Motor',
            ],
            [
                'maintenance_type_group_id' => 7,
                'name' => 'Revisión 5ª Rueda y/o King Pin',
            ],
            [
                'maintenance_type_group_id' => 7,
                'name' => 'Válvulas',
            ],
            [
                'maintenance_type_group_id' => 7,
                'name' => 'Prueba hidrostática',
            ],
            [
                'maintenance_type_group_id' => 7,
                'name' => 'Arreglos generales en el tanque',
            ],
            [
                'maintenance_type_group_id' => 7,
                'name' => 'Cambio disco quinta rueda',
            ],
            [
                'maintenance_type_group_id' => 7,
                'name' => 'Cambio de cauchos',
            ],
            [
                'maintenance_type_group_id' => 7,
                'name' => 'Alineación',
            ],
            [
                'maintenance_type_group_id' => 7,
                'name' => 'Tornamesa',
            ],
            [
                'maintenance_type_group_id' => 7,
                'name' => 'Cambio de Templetes',
            ],
            [
                'maintenance_type_group_id' => 8,
                'name' => 'Graduación de frenos',
            ],
            [
                'maintenance_type_group_id' => 8,
                'name' => 'Inspección sistema de frenos (campanas, bujes, levas, cámaras)',
            ],
            [
                'maintenance_type_group_id' => 8,
                'name' => 'Cambio de mangueras',
            ],
            [
                'maintenance_type_group_id' => 8,
                'name' => 'Cambio de botones de freno de seguridad',
            ],
            [
                'maintenance_type_group_id' => 8,
                'name' => 'Cambio de cámaras de seguridad',
            ],
            [
                'maintenance_type_group_id' => 8,
                'name' => 'Válvulas',
            ],
            [
                'maintenance_type_group_id' => 9,
                'name' => 'Cambio de aceite (Betico/Compresor)',
            ],
            [
                'maintenance_type_group_id' => 9,
                'name' => 'Cambio de aceite motor deutz',
            ],
            [
                'maintenance_type_group_id' => 9,
                'name' => 'Cambio de filtro aceite',
            ],
            [
                'maintenance_type_group_id' => 9,
                'name' => 'Cambio filtro de combustible',
            ],
            [
                'maintenance_type_group_id' => 9,
                'name' => 'Cambio de botones de freno de seguridad',
            ],
            [
                'maintenance_type_group_id' => 9,
                'name' => 'Cambio de culatas/válvulas',
            ],
            [
                'maintenance_type_group_id' => 9,
                'name' => 'Mangueras',
            ],
            [
                'maintenance_type_group_id' => 9,
                'name' => 'Motor deutz',
            ],
            [
                'maintenance_type_group_id' => 9,
                'name' => 'Betico',
            ],
            [
                'maintenance_type_group_id' => 10,
                'name' => 'Cambio de amortiguador de la cabina',
            ],
        ];

        // Inicializar la barra de progreso
        $this->command->info('Starting Seed Data ...');
        $bar = $this->command->getOutput()->createProgressBar(count($arrayData));

        foreach ($arrayData as $key => $value) {
            $data = new MaintenanceTypeInput;
            $data->maintenance_type_group_id = $value['maintenance_type_group_id'];
            $data->name = $value['name'];
            $data->order = $key + 10;
            $data->save();
        }

        $bar->finish(); // Finalizar la barra
    }
}
