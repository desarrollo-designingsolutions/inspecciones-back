<?php

namespace Database\Seeders;

use App\Models\InspectionTypeInput;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InspectionTypeInputSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //necesito que el order sea en seciuencia numerica

        $arrayData = [
            [
                'inspection_type_group_id' => 1,
                'name' => 'Tarjeta propiedad vehículo',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'SOAT',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Certificado Revisión Tecnicomecánica y de gas',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Póliza de Responsabilidad Civil y Extracontractual',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Cinturones de seguridad',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Luces: Altas, medias, direccionales, freno, parqueo y reversa',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Exhosto cubierto con aislamiento del tanque',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Botiquín',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Caja de herramientas',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => '2 Conos de 90 cms de alto',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Extintor de 20 Lbs o 5 en la cabina recargados',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Sistema eléctrico en buen estado',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Soñadoras de aislamiento de baterías',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Interruptor eléctrico central o master',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Espejos retrovisores en ambos lados en buen estado',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Dirección sin juegos',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Inspección General del Motor',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Tanque de Combustible con tapas, sin fugas ni goteos',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Vidrio panorámico sin fisuras',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Limpiavidrios',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Pito reverso',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Pito eléctrico',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Freno de Seguridad',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Llantas',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Aviso de prohibido llevar pasajeros',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Pasamanos de acceso a la unidad tractora',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Número UN acorde al producto',
            ],
            [
                'inspection_type_group_id' => 1,
                'name' => 'Kin Pin – Quinta Rueda – Tornamesa',
            ],
            [
                'inspection_type_group_id' => 2,
                'name' => 'Tanque sellado, sin fisuras, fugas ni abolladuras',
            ],
            [
                'inspection_type_group_id' => 2,
                'name' => 'Llantas en buen estado',
            ],
            [
                'inspection_type_group_id' => 2,
                'name' => 'Llantas de repuesto',
            ],
            [
                'inspection_type_group_id' => 2,
                'name' => '2 extintores de 20 Lbs recargados',
            ],
            [
                'inspection_type_group_id' => 2,
                'name' => 'Equipo para el control de derrames',
            ],
            [
                'inspection_type_group_id' => 2,
                'name' => 'Número UN acorde al producto en los tres costados',
            ],
            [
                'inspection_type_group_id' => 2,
                'name' => 'Equipo de seguridad',
            ],
            [
                'inspection_type_group_id' => 2,
                'name' => 'Defensas traseras',
            ],
            [
                'inspection_type_group_id' => 2,
                'name' => 'Conexiones eléctricas en buen estado y aisladas',
            ],
            [
                'inspection_type_group_id' => 2,
                'name' => 'Luces direccionales, parada, reverso, intermitente, parqueo',
            ],
            [
                'inspection_type_group_id' => 2,
                'name' => 'Válvulas sin goteos o fisuras',
            ],
            [
                'inspection_type_group_id' => 2,
                'name' => 'Barandaje para gases o líquidos sin rupturas',
            ],
            [
                'inspection_type_group_id' => 2,
                'name' => 'Barandaje sin averías, abolladuras ni fisuras',
            ],
            [
                'inspection_type_group_id' => 2,
                'name' => 'Aislantes en buen estado, anclaje intacto',
            ],
            [
                'inspection_type_group_id' => 3,
                'name' => 'Tornillos de sujeción al chasis bien apretados',
            ],
            [
                'inspection_type_group_id' => 3,
                'name' => 'Correas ajustadas y aseguradas',
            ],
            [
                'inspection_type_group_id' => 3,
                'name' => 'Mangueras sin fugas ni poros',
            ],
            [
                'inspection_type_group_id' => 3,
                'name' => 'Nivel de aceite',
            ],
            [
                'inspection_type_group_id' => 4,
                'name' => 'Realización Pausas Activas en la mañana',
            ],
            [
                'inspection_type_group_id' => 4,
                'name' => 'Realización Pausas Activas en la Tarde',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Cinturones de seguridad',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Luces (Altas, Medias, Direccionales, Freno, Parqueo y Reversa)',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Exhosto cubierto con aislamiento del tanque',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Botiquín',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Kit de carretera',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => '2 conos de 90 cms de alto',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Extintor de 20 lbs o 5 lbs en la cabina recargados',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Linternas',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Sistema eléctrico en buen estado',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Sistema de aislamiento de baterias',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Interruptor eléctrico central o master',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Espejos retrovisores en ambos lados en buen estado',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Dirección sin juegos',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Inspección general del motor',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Tanque de combustible con tapas, sin fugas ni goteos',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Vidrio panorámico sin fisuras',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Limpiavidrios',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Pito de reversa sonoro',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Pito eléctrico',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Freno de seguridad',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Llantas',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Aviso de prohibido llevar pasajeros',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Pasamanos de acceso a la unidad tractora',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Sillas en buen estado',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Número UN acorde al producto',
            ],
            [
                'inspection_type_group_id' => 5,
                'name' => 'Kin Pin - Quinta Rueda - Tornamesa',
            ],
            [
                'inspection_type_group_id' => 6,
                'name' => 'Tanque señalizado, sin fisuras, fugas ni abolladuras',
            ],
            [
                'inspection_type_group_id' => 6,
                'name' => 'Llantas con labrado notorio',
            ],
            [
                'inspection_type_group_id' => 6,
                'name' => 'Llantas de repuesto',
            ],
            [
                'inspection_type_group_id' => 6,
                'name' => '2 extintores de 20 Lbs recargados',
            ],
            [
                'inspection_type_group_id' => 6,
                'name' => 'Equipo para atención de derrames',
            ],
            [
                'inspection_type_group_id' => 6,
                'name' => 'Número UN acorde al producto en los tres costados',
            ],
            [
                'inspection_type_group_id' => 6,
                'name' => 'Defensa trasera',
            ],
            [
                'inspection_type_group_id' => 6,
                'name' => 'Conexiones eléctricas en buen estado y aisladas',
            ],
            [
                'inspection_type_group_id' => 6,
                'name' => 'Luces direccionales, parada, reverso, laterales, parqueo',
            ],
            [
                'inspection_type_group_id' => 6,
                'name' => 'Válvulas sin golpes o fisuras',
            ],
            [
                'inspection_type_group_id' => 6,
                'name' => 'Pernos del Manhol apretados',
            ],
            [
                'inspection_type_group_id' => 6,
                'name' => 'Bandeja para goteos sin fugas ni roturas',
            ],
            [
                'inspection_type_group_id' => 6,
                'name' => 'Punto de conexión estática',
            ],
            [
                'inspection_type_group_id' => 6,
                'name' => 'Acople rápido y tapa',
            ],
            [
                'inspection_type_group_id' => 7,
                'name' => 'Niveles de aceite adecuados para operar',
            ],
            [
                'inspection_type_group_id' => 7,
                'name' => 'Correas templadas y aseguradas de forma correcta',
            ],
            [
                'inspection_type_group_id' => 7,
                'name' => 'Tornillos asegurados',
            ],
            [
                'inspection_type_group_id' => 7,
                'name' => 'Trampa de condensación de fluidos sin obstrucciones',
            ],
            [
                'inspection_type_group_id' => 7,
                'name' => 'Elementos de sujeción sin oxidación o corrosión',
            ],
            [
                'inspection_type_group_id' => 7,
                'name' => 'Manómetro sin fisuras, golpes o averías',
            ],
            [
                'inspection_type_group_id' => 7,
                'name' => 'Mangueras sin fugas ni poros',
            ],
            [
                'inspection_type_group_id' => 7,
                'name' => 'Mangueras ajustadas en las bateas laterales, y sin amarres',
            ],
            [
                'inspection_type_group_id' => 8,
                'name' => 'Casco',
            ],
            [
                'inspection_type_group_id' => 8,
                'name' => 'Monógafas de Seguridad',
            ],
            [
                'inspection_type_group_id' => 8,
                'name' => 'Tapaoídos',
            ],
            [
                'inspection_type_group_id' => 8,
                'name' => 'Guantes',
            ],
            [
                'inspection_type_group_id' => 8,
                'name' => 'Protección Respiratoria',
            ],
            [
                'inspection_type_group_id' => 8,
                'name' => 'Botas de Seguridad',
            ],
            [
                'inspection_type_group_id' => 8,
                'name' => 'Ropa Adecuada',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Apósito estéril',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Cintas',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Ap��sito ocular',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Bajalenguas',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Esparadrapo',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Gasa',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Guantes desechables',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Inmovilizadores (cuello y ext.)',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Manta térmica',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Tijeras',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Venda elástica de 3 y 4',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Vendaje triangular',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Sulfapasta',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Sobres aftas camomilas naturales',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Isodine Espuma',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Isodine Solución',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Agua Pura en Botella o Bolsa',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Manual de primeros auxilios, listado de los elementos del botiquín',
            ],
            [
                'inspection_type_group_id' => 9,
                'name' => 'Mascarilla facial de bolsillo para RCP',
            ],
            [
                'inspection_type_group_id' => 10,
                'name' => 'Fecha de recarga, agente y capacidad: Sep 24',
            ],
            [
                'inspection_type_group_id' => 10,
                'name' => 'Boquilla y manguera',
            ],
            [
                'inspection_type_group_id' => 10,
                'name' => 'Manijas',
            ],
            [
                'inspection_type_group_id' => 10,
                'name' => 'Presión del Manómetro',
            ],
            [
                'inspection_type_group_id' => 10,
                'name' => 'Corrosión',
            ],
            [
                'inspection_type_group_id' => 10,
                'name' => 'Acople',
            ],
            [
                'inspection_type_group_id' => 11,
                'name' => 'Fecha de recarga, agente y capacidad: Nov 24',
            ],
            [
                'inspection_type_group_id' => 11,
                'name' => 'Boquilla y manguera',
            ],
            [
                'inspection_type_group_id' => 11,
                'name' => 'Manijas',
            ],
            [
                'inspection_type_group_id' => 11,
                'name' => 'Presión del Manómetro',
            ],
            [
                'inspection_type_group_id' => 11,
                'name' => 'Corrosión',
            ],
            [
                'inspection_type_group_id' => 11,
                'name' => 'Acople',
            ],
            [
                'inspection_type_group_id' => 12,
                'name' => 'Barreras absorbentes',
            ],
            [
                'inspection_type_group_id' => 12,
                'name' => 'Material absorbente',
            ],
            [
                'inspection_type_group_id' => 12,
                'name' => '10 bolsas plásticas',
            ],
            [
                'inspection_type_group_id' => 12,
                'name' => 'Pala con cabo antichispa',
            ],
            [
                'inspection_type_group_id' => 12,
                'name' => '10 metros de manila de 1/2',
            ],
            [
                'inspection_type_group_id' => 12,
                'name' => 'Masilla epóxica',
            ],
            [
                'inspection_type_group_id' => 12,
                'name' => '4 cuñas de madera',
            ],
            [
                'inspection_type_group_id' => 12,
                'name' => 'Martillo de hule',
            ],
            [
                'inspection_type_group_id' => 12,
                'name' => 'Cinta demarcación',
            ],
            [
                'inspection_type_group_id' => 12,
                'name' => 'Guantes en nitrilo',
            ],
            [
                'inspection_type_group_id' => 12,
                'name' => 'Guantes de vaqueta',
            ],
            [
                'inspection_type_group_id' => 12,
                'name' => 'Elemento de protección respiratoria (Mascarilla de protección)',
            ],
            [
                'inspection_type_group_id' => 12,
                'name' => 'Gafas de seguridad',
            ],
            [
                'inspection_type_group_id' => 12,
                'name' => 'Balde plástico de 5 litros',
            ],
            [
                'inspection_type_group_id' => 12,
                'name' => '5 m2 de plástico grueso',
            ],
            [
                'inspection_type_group_id' => 12,
                'name' => '500 cm³ de jabón desengrasante',
            ],
            [
                'inspection_type_group_id' => 12,
                'name' => 'Paños absorbentes',
            ],
            [
                'inspection_type_group_id' => 12,
                'name' => 'Chaleco reflectivo',
            ],
        ];

        // Inicializar la barra de progreso
        $this->command->info('Starting Seed Data ...');
        $bar = $this->command->getOutput()->createProgressBar(count($arrayData));

        foreach ($arrayData as $key => $value) {
            $data = new InspectionTypeInput;
            $data->inspection_type_group_id = $value["inspection_type_group_id"];
            $data->name = $value["name"];
            $data->order = $key + 10;
            $data->save();
        }

        $bar->finish(); // Finalizar la barra

    }
}
