<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspección Camión de Vacío</title>
    <style>
        @page {
            size: A4;
            /* Configuración de tamaño de página */
            margin: 5mm;
            /* Márgenes mínimos */
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        td,
        th {
            border: 1px solid black;
            padding: 2px;
            text-align: left;
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
    </style>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th colspan="3" style="width: 20%; text-align: center;">
                    <img src="{{ public_path('storage\companies\company_9e21d8d4-e4fc-410b-b730-853b536b1634\XzoX3lFmstYsB1ZkGMJyqEbSVyEtdRXSSKdGsVZ6.png') }}"
                        alt="logo" style="max-width: 100px; max-height: 50px;">
                </th>
                <th colspan="13" style="width: 80%; text-align: center;">
                    @if ($data['inspection_type_id'] == 1)
                        SISTEMA INTEGRADO DE GESTION PRE OPERACIONAL
                    @else
                        SISTEMA INTEGRADO DE GESTION HSEQ
                    @endif

                    <br>
                    INSPECCIÓN CAMIÓN DE VACÍO
                </th>
            </tr>
            <tr>
                <th colspan="4">Código: HSEQ-F-03</th>
                <th colspan="4">FECHA: 19/12/2023</th>
                <th colspan="4">VERSIÓN: 11</th>
                <th colspan="4">PÁGINA: 1/2</th>
            </tr>
        </thead>
    </table>
    <table>
        <thead>
            <tr>
                <td colspan="6">Fecha de inspección: <b>{{ $data['inspection_date'] }}</b></td>
                <td colspan="10">Ciudad de inspección: <b>{{ $data['city'] }}</b></td>
            </tr>

            <tr>
                <th colspan="16" style="text-align: center;">DATOS DEL CONDUCTOR Y VEHICULO</th>
            </tr>
            <tr>
                <td colspan="4">Nombre</td>
                <td colspan="4">{{ $data['operator']['name'] }}</td>
                <td colspan="4">PLACA VEHÍCULO</td>
                <td colspan="4">{{ $data['vehicle']['license_plate'] }}</td>
            </tr>
            <tr>
                <td colspan="4">C.C.</td>
                <td colspan="4">{{ $data['operator']['document'] }}</td>
                <td colspan="4">MARCA Y MODELO</td>
                <td colspan="4">{{ $data['vehicle']['brand_vehicle_name'] }} {{ $data['vehicle']['model'] }}</td>
            </tr>
            <tr>
                <td colspan="4">Licencia de Conducción</td>
                <td colspan="4">{{ $data['operator']['license'] }}</td>
                <td colspan="3">RIGIDO</td>
                <td colspan="1" width="40px" style="text-align: center">
                    {{ $data['vehicle']['vehicle_structure_name'] == 'Rigido' ? 'X' : null }}</td>
                <td colspan="3">ARTICULADO</td>
                <td colspan="1" width="40px" style="text-align: center">
                    {{ $data['vehicle']['vehicle_structure_name'] == 'Articulado' ? 'X' : null }}</td>
            </tr>
        </thead>
    </table>
    <table>
        <thead>
            <tr>
                <th colspan="4">DOCUMENTOS</th>
            </tr>
            <tr>
                <th width="30%"></th>
                <th>Numero</th>
                <th>Vence</th>
                <th>Original (S/N)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['documents'] as $document)
                <tr>
                    <td>{{ $document['name'] }}</td>
                    <td style="text-align: center">{{ $document['document'] }}</td>
                    <td style="text-align: center">{{ $document['expiration_date'] }}</td>
                    <td style="text-align: center">{{ $document['original'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <table>
        <tbody>
            <tr>
                <th colspan="5">INSPECCIÓN DEL VEHICULO</th>
            </tr>
            @if ($data['inspection_type_id'] == 1)
                @foreach ($data['inspectionInputResponses'] as $group)
                    <tr>
                        <th>{{ $group['name'] }}</th>

                        @foreach ($data['getResponseVehicle'] as $resp)
                            <th>{{ $resp['title'] }}</th>
                        @endforeach
                    </tr>

                    @foreach ($group['inspectionTypeInputs'] as $input)
                        <tr>
                            <td>{{ $input['name'] }}</td>
                            @foreach ($input['responses'] as $response)
                                <td style="text-align: center">{{ $response }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
            @else
                @foreach ($data['inspectionInputResponses'] as $group)
                    <tr>
                        <th>{{ $group['name'] }}</th>

                        @foreach ($data['getResponseVehicle'] as $resp)
                            <th>{{ $resp['title'] }}</th>
                        @endforeach
                    </tr>

                    @foreach ($group['inspectionTypeInputs'] as $input)
                        <tr>
                            <td>{{ $input['name'] }}</td>
                            @foreach ($input['responses'] as $response)
                                <td style="text-align: center">{{ $response }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
            @endif
        </tbody>
    </table>
    <table>
        <thead>
            <tr>
                <td><b>OBSERVACIONES GENERALES: </b>{{ $data['general_comment'] }}</td>
            </tr>
        </thead>
    </table>
    <table>
        <tr>
            <th colspan="8">Nombre y firma del operador</th>
            <th colspan="8">Nombre y firma de la persona que inspecciona</th>
        </tr>
        <tr>
            <td colspan="8" style="height: 50px;"></td>
            <td colspan="8" style="height: 50px;"></td>
        </tr>
    </table>

</body>

</html>
