<?php

namespace Database\Seeders;

use App\Models\MaintenanceTypeInput;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaintenanceTypeInputSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arrayData =[
            ['id' => 1, 'maintenance_type_group_id' => 1, 'name' => 'Cambio de aceite motor'],
            ['id' => 2, 'maintenance_type_group_id' => 1, 'name' => 'Cambio filtro de aceite, de combustible y filtro de agua'],
            ['id' => 3, 'maintenance_type_group_id' => 1, 'name' => 'Cambio filtro de aire'],
            ['id' => 4, 'maintenance_type_group_id' => 1, 'name' => 'Cambio de valvulina'],
            ['id' => 5, 'maintenance_type_group_id' => 1, 'name' => 'Engrase general del vehículo'],
            ['id' => 6, 'maintenance_type_group_id' => 1, 'name' => 'Cambio valvulina'],
            ['id' => 7, 'maintenance_type_group_id' => 1, 'name' => 'Cambio de Refrigerante'],
            ['id' => 8, 'maintenance_type_group_id' => 1, 'name' => 'Cambio de barillas de medición de niveles'],
            ['id' => 9, 'maintenance_type_group_id' => 1, 'name' => 'Radiador (Tanque de agua)'],
            ['id' => 10, 'maintenance_type_group_id' => 1, 'name' => 'Cambio de sensores'],
            ['id' => 11, 'maintenance_type_group_id' => 1, 'name' => 'Inyectores'],
            ['id' => 12, 'maintenance_type_group_id' => 1, 'name' => 'Motor'],
            ['id' => 13, 'maintenance_type_group_id' => 2, 'name' => 'Caja de la dirección'],
            ['id' => 14, 'maintenance_type_group_id' => 2, 'name' => 'Retenedores de aceite hidráulico'],
            ['id' => 15, 'maintenance_type_group_id' => 2, 'name' => 'Crucetas'],
            ['id' => 16, 'maintenance_type_group_id' => 2, 'name' => 'Mangueras'],
            ['id' => 17, 'maintenance_type_group_id' => 2, 'name' => 'Bomba hidráulica'],
            ['id' => 18, 'maintenance_type_group_id' => 2, 'name' => 'Barra estabilizadora'],
            ['id' => 19, 'maintenance_type_group_id' => 2, 'name' => 'Alineación'],
            ['id' => 20, 'maintenance_type_group_id' => 2, 'name' => 'Spínder'],
            ['id' => 21, 'maintenance_type_group_id' => 3, 'name' => 'Disco del cloutch (embrague)'],
            ['id' => 22, 'maintenance_type_group_id' => 3, 'name' => 'Prensa del cloutch (embrague)'],
            ['id' => 23, 'maintenance_type_group_id' => 3, 'name' => 'Balinera del embrague'],
            ['id' => 24, 'maintenance_type_group_id' => 3, 'name' => 'Niveles de aceite de la transmisión (caja)'],
            ['id' => 25, 'maintenance_type_group_id' => 3, 'name' => 'Niveles de aceite de los diferenciales'],
            ['id' => 26, 'maintenance_type_group_id' => 3, 'name' => 'Yoke del cardan primario y secundario'],
            ['id' => 27, 'maintenance_type_group_id' => 3, 'name' => 'Crucetas del cardan primario y secundario'],
            ['id' => 28, 'maintenance_type_group_id' => 3, 'name' => 'Engrases del sistema (lubricación)'],
            ['id' => 29, 'maintenance_type_group_id' => 3, 'name' => 'Diferenciales (corona, spig, planetarios)'],
            ['id' => 30, 'maintenance_type_group_id' => 3, 'name' => 'Ejes de diferenciales'],
            ['id' => 31, 'maintenance_type_group_id' => 3, 'name' => 'Retenedores de los ejes'],
            ['id' => 32, 'maintenance_type_group_id' => 3, 'name' => 'Rodamientos'],
            ['id' => 33, 'maintenance_type_group_id' => 3, 'name' => 'Tandem'],
            ['id' => 34, 'maintenance_type_group_id' => 3, 'name' => 'Pernos'],
            ['id' => 35, 'maintenance_type_group_id' => 3, 'name' => 'Llantas de repuesto'],
            ['id' => 36, 'maintenance_type_group_id' => 3, 'name' => 'Llantas delanteras'],
            ['id' => 37, 'maintenance_type_group_id' => 3, 'name' => 'Llantas traseras'],
            ['id' => 38, 'maintenance_type_group_id' => 3, 'name' => 'Cambio de rodajas'],
            ['id' => 39, 'maintenance_type_group_id' => 3, 'name' => 'Cambio de espárragos'],
            ['id' => 40, 'maintenance_type_group_id' => 3, 'name' => 'Troque'],
            ['id' => 41, 'maintenance_type_group_id' => 4, 'name' => 'Muelles delanteros y traseros'],
            ['id' => 42, 'maintenance_type_group_id' => 4, 'name' => 'Bombonas traseras y delanteras'],
            ['id' => 43, 'maintenance_type_group_id' => 4, 'name' => 'Tornillos centrales de muelle'],
            ['id' => 44, 'maintenance_type_group_id' => 4, 'name' => 'Grapas de muelles'],
            ['id' => 45, 'maintenance_type_group_id' => 4, 'name' => 'Tensores traseros y delanteros'],
            ['id' => 46, 'maintenance_type_group_id' => 4, 'name' => 'Balancines'],
            ['id' => 47, 'maintenance_type_group_id' => 4, 'name' => 'Pasadores de balancines y tensores'],
            ['id' => 48, 'maintenance_type_group_id' => 4, 'name' => 'Pasadores de viga'],
            ['id' => 49, 'maintenance_type_group_id' => 4, 'name' => 'Bujes'],
            ['id' => 50, 'maintenance_type_group_id' => 4, 'name' => 'Bandas'],
            ['id' => 51, 'maintenance_type_group_id' => 4, 'name' => 'Corbatines'],
            ['id' => 52, 'maintenance_type_group_id' => 5, 'name' => 'Sistema de baterías'],
            ['id' => 53, 'maintenance_type_group_id' => 5, 'name' => 'Revisión sistema eléctrico general (incluye luces)'],
            ['id' => 54, 'maintenance_type_group_id' => 5, 'name' => 'Alternador'],
            ['id' => 55, 'maintenance_type_group_id' => 5, 'name' => 'Arranque'],
            ['id' => 56, 'maintenance_type_group_id' => 5, 'name' => 'Cables'],
            ['id' => 57, 'maintenance_type_group_id' => 6, 'name' => 'Cambio correas de la bomba de vacío'],
            ['id' => 58, 'maintenance_type_group_id' => 6, 'name' => 'Cambio aceite de la bomba de vacío'],
            ['id' => 59, 'maintenance_type_group_id' => 6, 'name' => 'Cambio de filtro'],
            ['id' => 60, 'maintenance_type_group_id' => 6, 'name' => 'Motor'],
            ['id' => 61, 'maintenance_type_group_id' => 7, 'name' => 'Revisión 5ª Rueda y/o King Pin'],
            ['id' => 62, 'maintenance_type_group_id' => 7, 'name' => 'Válvulas'],
            ['id' => 63, 'maintenance_type_group_id' => 7, 'name' => 'Prueba hidrostática'],
            ['id' => 64, 'maintenance_type_group_id' => 7, 'name' => 'Arreglos generales en el tanque'],
            ['id' => 65, 'maintenance_type_group_id' => 7, 'name' => 'Cambio disco quinta rueda'],
            ['id' => 66, 'maintenance_type_group_id' => 7, 'name' => 'Cambio de cauchos'],
            ['id' => 67, 'maintenance_type_group_id' => 7, 'name' => 'Alineación'],
            ['id' => 68, 'maintenance_type_group_id' => 7, 'name' => 'Tornamesa'],
            ['id' => 69, 'maintenance_type_group_id' => 7, 'name' => 'Cambio de Templetes'],
            ['id' => 70, 'maintenance_type_group_id' => 8, 'name' => 'Graduación de frenos'],
            ['id' => 71, 'maintenance_type_group_id' => 8, 'name' => 'Inspección sistema de frenos (campanas, bujes, levas, cámaras)'],
            ['id' => 72, 'maintenance_type_group_id' => 8, 'name' => 'Cambio de mangueras'],
            ['id' => 73, 'maintenance_type_group_id' => 8, 'name' => 'Cambio de botones de freno de seguridad'],
            ['id' => 74, 'maintenance_type_group_id' => 8, 'name' => 'Cambio de cámaras de seguridad'],
            ['id' => 75, 'maintenance_type_group_id' => 8, 'name' => 'Válvulas'],
            ['id' => 76, 'maintenance_type_group_id' => 9, 'name' => 'Cambio de aceite (Betico/Compresor)'],
            ['id' => 77, 'maintenance_type_group_id' => 9, 'name' => 'Cambio de aceite motor deutz'],
            ['id' => 78, 'maintenance_type_group_id' => 9, 'name' => 'Cambio de filtro aceite'],
            ['id' => 79, 'maintenance_type_group_id' => 9, 'name' => 'Cambio filtro de combustible'],
            ['id' => 80, 'maintenance_type_group_id' => 9, 'name' => 'Cambio de botones de freno de seguridad'],
            ['id' => 81, 'maintenance_type_group_id' => 9, 'name' => 'Cambio de culatas/válvulas'],
            ['id' => 82, 'maintenance_type_group_id' => 9, 'name' => 'Mangueras'],
            ['id' => 83, 'maintenance_type_group_id' => 9, 'name' => 'Motor deutz'],
            ['id' => 84, 'maintenance_type_group_id' => 9, 'name' => 'Betico'],
            ['id' => 85, 'maintenance_type_group_id' => 10, 'name' => 'Cambio de amortiguador de la cabina'],
        ];



         // Inicializar la barra de progreso
         $this->command->info('Starting Seed Data ...');
         $bar = $this->command->getOutput()->createProgressBar(count($arrayData));

         foreach ($arrayData as $key => $value) {
             $data = new MaintenanceTypeInput();
             $data->inspection_type_group_id = $value['inspection_type_group_id'];
             $data->name = $value['name'];
             $data->order = $key + 10;
             $data->save();
         }

         $bar->finish(); // Finalizar la barra
    }
}
