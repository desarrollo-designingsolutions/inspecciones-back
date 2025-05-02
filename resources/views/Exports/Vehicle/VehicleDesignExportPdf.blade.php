<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspección Camión de Vacío</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 5mm;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            /* de 12px a 10px */
            margin: 5mm;
        }

        td,
        th {
            border: 1px solid black;
            padding: 1px;
            /* de 2px a 1px */
        }

        th {
            text-align: center;
        }

        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .observations {
            margin-top: 10px;
            font-size: 12px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

@php
function getResponseByIdAndDay($responses, $day)
{
$responseItem = collect($responses)->firstWhere('day', $day);
if ($responseItem) {
$response = $responseItem['response'];
switch ($response) {
case 'good':
return 'B';
case 'regular':
return 'R';
case 'bad':
return 'M';
case 'not applicable':
return 'NA';
case 'complies':
return 'C';
case 'does not comply':
return 'NC';
default:
return $response;
}
}
return '';
}

// Procesar operadores por día
$operatorsByDay = [];
foreach ($data['inspection_details'] as $detail) {
$date = \Carbon\Carbon::parse($detail['inspection_date']);
$day = $date->day; // Obtener el día del mes (1-31)
$operatorsByDay[$day] = $detail['user_operator'] ?? 'Sin operador';
}
@endphp


<body>
    <div>
        <table style="border: 0.5px solid black; border-collapse: collapse; width: 100%;">
            <thead>
                <tr>
                    <th colspan="2"></th>
                    <th colspan="3" style="padding: 10px;">
                        <div style="display: flex; justify-content: center; align-items: center; text-align: center;">
                            <img src="{{ public_path('storage/logo_ochoa.png') }}"
                                alt="Logo" style="max-width: 100px; max-height: 50px;">
                        </div>
                    </th>
                    <th colspan="31" style="text-align: center; font-weight: bold;">
                        SISTEMA DE GESTIÓN HSEQ
                        <br />
                        INSPECCIÓN PREOPERACIONAL SISTEMAS DE VACIO, MANGUERAS Y ACOPLES OPERACIÓN PERENCO
                    </th>
                </tr>

                <tr>
                    <th colspan="5" style="text-align: center; font-weight: bold; background: gray;">
                        Código: HSEQ-F-25
                    </th>
                    <th colspan="10" style="text-align: center;">
                        Fecha:
                    </th>
                    <th colspan="10" style="text-align: center;">
                        Versión: 3
                    </th>
                    <th colspan="11" style="text-align: center; font-weight: bold; background: gray;">
                        Página: 1 de 1
                    </th>
                </tr>

                <tr>
                    <th colspan="5">
                        No. Identificación:
                    </th>
                    <th colspan="10">
                        Tipo de Manguera:
                    </th>
                    <th colspan="10">
                        Longitud:
                    </th>
                    <th colspan="11">
                        Tipo de Manguera:
                    </th>
                </tr>

                <tr>
                    <th colspan="5">
                        Mes y año de la inspección: {{ $data['data']['month'] ?? '' }} - {{ $data['data']['year'] ?? '' }}
                    </th>
                    <th colspan="15">
                        Nombre del operador:
                        {{ collect($operatorsByDay)->filter()->sortKeys()->first() ?? '' }}
                    </th>
                    <th colspan="16">
                        Placa Vehículo: {{ $data['data']['license_plate'] }}
                    </th>
                </tr>

                <tr>
                    <th colspan="5" style="text-align: center; font-weight: bold;">
                        SISTEMAS DE VACIO
                    </th>
                    <th colspan="31" style="text-align: center; font-weight: bold;">
                        DÍA
                    </th>
                </tr>

                @foreach ($data['inspections'] as $inspectionTab)
                <tr>
                    <th colspan="1" style="text-align: center; font-weight: bold;">
                        item
                    </th>
                    <th colspan="4" style="text-align: center; font-weight: bold; text-transform: uppercase;">
                        {{ $inspectionTab['name'] }}
                    </th>
                    @for ($i = 1; $i <= 31; $i++)
                        <th style="text-align:center; font-weight:bold;">
                        {{-- Solo mostramos el número si existe ese día en el mes --}}
                        @if($i <= $data['data']['days'])
                            {{ $i }}
                            @endif
                            </th>
                            @endfor
                </tr>

                @foreach ($inspectionTab['inspection_type_inputs'] as $inspectionInput)
                <tr>
                    <th colspan="1">
                        {{ $loop->iteration }}
                    </th>
                    <th colspan="4" style="text-align:left;">
                        {{ $inspectionInput['name'] }}
                    </th>
                    @for ($i = 1; $i <= 31; $i++)
                        <th style="text-align:center;">
                        {{-- Solo invocamos la función si ese día existe, si no queda en blanco --}}
                        @if($i <= $data['data']['days'])
                            {{ getResponseByIdAndDay($inspectionInput['inspection_input_responses'], $i) }}
                            @endif
                            </th>
                            @endfor
                </tr>
                @endforeach

                @endforeach
                <tr>
                    <th colspan="36">
                        Califique B (Bueno), M (Malo), NA (No Aplica), C (Cumple), NC (No Cumple), R (Regular)
                    </th>
                </tr>
                <tr>
                    <th colspan="36" style="text-align:left; height: 50px; vertical-align: top;">
                        OBSERVACIONES:
                    </th>
                </tr>
            </thead>
        </table>

        @php
        // Asegurémonos de que las claves (días) estén ordenadas
        ksort($operatorsByDay);
        @endphp

        <div div class="page-break"></div>

        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr>
                    <th style="border:1px solid black; padding:4px; text-align:center; font-weight:bold;">DÍA</th>
                    <th style="border:1px solid black; padding:4px; text-align:center; font-weight:bold;">NOMBRE OPERADOR DE ACUERDO A ASIGNACIÓN DIARIA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($operatorsByDay as $day => $operator)
                @if(! empty($operator))
                <tr>
                    <td style="border:1px solid black; padding:4px; text-align:center;">{{ $day }}</td>
                    <td style="border:1px solid black; padding:4px;">
                        {{ $operator }}
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>