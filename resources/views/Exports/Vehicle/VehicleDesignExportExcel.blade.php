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
foreach ($inspections['inspection_details'] as $detail) {
$date = \Carbon\Carbon::parse($detail['inspection_date']);
$day = $date->day; // Obtener el día del mes (1-31)
$operatorsByDay[$day] = $detail['user_operator'] ?? 'Sin operador';
}
@endphp

<div>
    <table style="border: 0.5px solid black; border-collapse: collapse;">
        <thead>
            <tr>
                <th colspan="2"></th>
                <th colspan="3" style="padding: 10px;">
                    <div style="display: flex; justify-content: center; align-items: center; text-align: center;">
                        <img src="{{ public_path('storage/logo_ochoa.png') }}"
                            alt="Logo" width="45" height="45">
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
                    Mes y año de la inspección: {{ $data['month'] ?? '' }} - {{ $data['year'] ?? '' }}
                </th>
                <th colspan="15">
                    Nombre del operador:
                    {{ collect($operatorsByDay)->filter()->sortKeys()->first() ?? '' }}
                </th>
                <th colspan="16">
                    Placa Vehículo: {{ $data['license_plate'] }}
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

            @foreach ($inspections['inspections'] as $inspectionTab)
            <tr>
                <th colspan="1" style="text-align: center; font-weight: bold;">
                    item
                </th>
                <th colspan="4" style="text-align: center; font-weight: bold; text-transform: uppercase;">
                    {{ $inspectionTab['name'] }}
                </th>
                @for ($i = 1; $i <= $data['days']; $i++)
                    <th colspan="1" style="text-align: center; font-weight: bold;">{{ $i }}</th>
                    @endfor
            </tr>

            @foreach ($inspectionTab['inspection_type_inputs'] as $inspectionInput)
            <tr>
                <th colspan="1" style="text-align: center;">
                    {{ $loop->iteration }}
                </th>
                <th colspan="4" style="text-align:left;">
                    {{ $inspectionInput['name'] }}
                </th>
                @for ($i = 1; $i <= $data['days']; $i++)
                    <th colspan="1" style="text-align: center; font-weight: bold;">
                    {{ getResponseByIdAndDay($inspectionInput['inspection_input_responses'], $i) }}
                    </th>
                    @endfor
            </tr>
            @endforeach
            @endforeach

            <tr>
                <th colspan="36" style="text-align: center;">
                    Califique B (Bueno), M (Malo), NA (No Aplica), C (Cumple), NC (No Cumple), R (Regular)
                </th>
            </tr>
            <tr>
                <th colspan="36" style="text-align:left; height: 50px; vertical-align: top;">
                    OBSERVACIONES:
                </th>
            </tr>

            <tr>
                <th colspan="5" style="text-align: center; font-weight: bold;">
                    NOMBRE OPERADOR DE ACUERDO A ASIGNACIÓN DIARIA
                </th>
                @for ($i = 1; $i <= $data['days']; $i++)
                    <th colspan="1" style="text-align: center; white-space: normal; word-wrap: break-word; min-width: 50px;">
                    {{ $operatorsByDay[$i] ?? '' }}
                    </th>
                    @endfor
            </tr>

        </thead>
    </table>
</div>