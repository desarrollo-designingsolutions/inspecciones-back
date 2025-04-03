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
@endphp

<div>
    <table style="border: 0.5px solid black; border-collapse: collapse;">
        <thead>
            <tr>
                <th colspan="2"></th>
                <th colspan="3" style="padding: 10px;">
                    <div style="display: flex; justify-content: center; align-items: center; text-align: center;">
                        <img src="https://api.inspecciones.desarrollo.com.co/storage/companies/company_9e21d8d4-e4fc-410b-b730-853b536b1634/en9ZVxNUhRGB1omZsvlnHfqSKTvhhD5PXi3GVxA4.png"
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
                    Código:
                </th>
                <th colspan="10" style="text-align: center;">
                    Fecha: {{ $inspections['inspection_details'][0]['inspection_date'] ?? '' }}
                </th>
                <th colspan="10" style="text-align: center;">
                    Versión:
                </th>
                <th colspan="11" style="text-align: center; font-weight: bold; background: gray;">
                    Página:
                </th>
            </tr>

            <tr>
                <th colspan="5">
                    No. Identificación: {{ $inspections['inspection_details'][0]['id'] ?? '' }}
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
                    <th colspan="3" style="text-align: center; font-weight: bold;">
                        {{ $inspectionTab['name'] }}
                    </th>
                    <th colspan="1" style="text-align: center; font-weight: bold;">
                        APLICA
                    </th>
                    <th colspan="1" style="text-align: center; font-weight: bold;">
                        NO APLICA
                    </th>
                    @for ($i = 1; $i <= $data['days']; $i++)
                        <th colspan="1" style="text-align: center; font-weight: bold;">{{ $i }}</th>
                    @endfor
                </tr>

                @foreach ($inspectionTab['inspection_type_inputs'] as $inspectionInput)
                    <tr>
                        <th colspan="5">
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
                <th colspan="36"></th>
            </tr>

            <tr>
                <th colspan="36"></th>
            </tr>

            <tr>
                <th colspan="36">
                    Califique B (Bueno), M (Malo), NA (No Aplica), C (Cumple), NC (No Cumple), R (Regular)
                </th>
            </tr>
            <tr>
                <th colspan="36">
                    OBSERVACIONES:
                    {{ implode(', ', array_column($inspections['inspection_details'], 'general_comment')) }}
                </th>
            </tr>
        </thead>
    </table>
</div>
