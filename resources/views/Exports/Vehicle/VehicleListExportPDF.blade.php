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
                    <img src="{{ public_path('images/logo_ochoa.jpeg') }}" alt="logo"
                        style="max-width: 100px; max-height: 50px;">
                </th>
                <th colspan="13" style="width: 80%; text-align: center;">

                    SISTEMA INTEGRADO DE GESTION HSEQ

                    <br>
                    PROGRAMA DE INTEGRIDAD MECANICA
                </th>
            </tr>
            <tr>
                <th colspan="4">Codigo: GO-DSG-01</th>
                <th colspan="4">Fecha: 14/02/2023</th>
                <th colspan="4">VERSIÓN: 6</th>
                <th colspan="4">PÁGINA: 1/1</th>
            </tr>
        </thead>
    </table>
    <table>
        <thead>

            <tr>
                <th colspan="16" style="text-align: center;">INFORMACIÓN GENERAL</th>
            </tr>
            <tr>
                <td colspan="4" style="font-weight: bold">PLACA</td>
                <td colspan="4">{{ $data['vehicle']->license_plate }}</td>
                <td colspan="4" style="font-weight: bold">CLASE VEHÍCULO</td>
                <td colspan="4">{{ $data['vehicle']->type_vehicle->name }}</td>
            </tr>
            <tr>
                <td colspan="4" style="font-weight: bold">FECHA DE MATRICULA</td>
                <td colspan="4">{{ \Carbon\Carbon::parse($data['vehicle']->date_registration)->format('d/m/Y') }}
                </td>
                <td colspan="4" style="font-weight: bold">NÚMERO DE MOTOR</td>
                <td colspan="4">{{ $data['vehicle']->engine_number }}</td>
            </tr>
            <tr>
                <td colspan="4" style="font-weight: bold">MODELO</td>
                <td colspan="4">{{ $data['vehicle']->model }}</td>
                <td colspan="4" style="font-weight: bold">MARCA DEL VEHICULO</td>
                <td colspan="4">{{ $data['vehicle']->brand_vehicle->name }}</td>
            </tr>
            <tr>
                <td colspan="4" style="font-weight: bold">DEPARTAMENTO DE OPERACIÓN</td>
                <td colspan="4">{{ $data['vehicle']->state->name }}</td>
                <td colspan="4" style="font-weight: bold">CIUDAD DE OPERACIÓN</td>
                <td colspan="4">{{ $data['vehicle']->city->name }}</td>
            </tr>
            <tr>
                <td colspan="4" style="font-weight: bold">NÚMERO DE VIN</td>
                <td colspan="4">{{ $data['vehicle']->vin_number }}</td>
                <td colspan="4" style="font-weight: bold">CAPACIDAD DE CARGA (KG)</td>
                <td colspan="4">{{ $data['vehicle']->load_capacity }}</td>
            </tr>
            <tr>
                <td colspan="4" style="font-weight: bold">CLIENTE</td>
                <td colspan="4">{{ $data['vehicle']->client->name }}</td>
                <td colspan="4" style="font-weight: bold">PESO BRUTO (KG)</td>
                <td colspan="4">{{ $data['vehicle']->gross_vehicle_weight }}</td>
            </tr>
            <tr>
                <td colspan="4" style="font-weight: bold">CAPACIDAD DE PASAJEROS</td>
                <td colspan="4">{{ $data['vehicle']->passenger_capacity }}</td>
                <td colspan="4" style="font-weight: bold">NÚMERO DE EJES</td>
                <td colspan="4">{{ $data['vehicle']->number_axles }}</td>
            </tr>
            <tr>
                <td colspan="4" style="font-weight: bold">KILOMETRAJE</td>
                <td colspan="4">{{ $data['vehicle']->current_mileage }}</td>
                <td colspan="4" style="font-weight: bold">ESTRUCTURA</td>
                <td colspan="4">{{ $data['vehicle']->vehicle_structure->name }}</td>
            </tr>
            <tr>
                <td colspan="4" style="font-weight: bold">TRAILER</td>
                <td colspan="4">{{ $data['vehicle']->have_trailer ? 'X' : '' }}</td>
                <td colspan="4" style="font-weight: bold">NÚMERO DE TRAILER</td>
                <td colspan="4">{{ $data['vehicle']->have_trailer ? $data['vehicle']->trailer : '' }}</td>
            </tr>
        </thead>
    </table>
    <table>
        <thead>
            <tr>
                <th colspan="3">DOCUMENTOS</th>
            </tr>
            <tr>
                <th width="50%">TIPO DE DOCUMENTO</th>
                <th>Numero</th>
                <th>Vence</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['vehicle']->type_documents as $document)
                <tr>
                    <td>{{ $document->type_document->name }}</td>
                    <td style="text-align: center">{{ $document->document_number }}</td>
                    <td style="text-align: center">{{ $document->expiration_date }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <thead>
            <tr>
                <th colspan="2">FOTOS</th>
            </tr>
            <tr>
                <th width="50%">Foto Frontal</th>
                <th width="50%">Foto Reverso</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center; vertical-align: middle;">
                    <img src="{{ public_path('storage/' . $data['vehicle']->photo_front) }}"
                        style="width: 370px; height: 210px;"
                        alt="Foto del vehículo">
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <img src="{{ public_path('storage/' . $data['vehicle']->photo_rear) }}"
                        style="width: 370px; height: 210px;"
                        alt="Foto del vehículo">
                </td>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th width="50%">Foto Lado Derecho</th>
                <th width="50%">Foto Lado Izquierdo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center; vertical-align: middle;">
                    <img src="{{ public_path('storage/' . $data['vehicle']->photo_right_side) }}"
                        style="width: 370px; height: 210px;"
                        alt="Foto del vehículo">
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <img src="{{ public_path('storage/' . $data['vehicle']->photo_left_side) }}"
                        style="width: 370px; height: 210px;"
                        alt="Foto del vehículo">
                </td>
            </tr>
        </tbody>
    </table>

    <table>
        @if (count($data['table']) > 1)
            <thead>
                <tr>
                    <th colspan="12">REGISTRO MANTENIMIENTOS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['table'] as $item)
                    <tr>

                        @foreach ($item as $item2)
                            <th style="width: 1rem; font-size: 8px; font-weight: bold">{{ $item2 }}
                            </th>
                        @endforeach

                    </tr>
                @endforeach
            </tbody>
        @else
            <thead>
                <tr>
                    <th colspan="12">REGISTRO MANTENIMIENTOS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center" colspan="12">No hay registros de mantenimientos</td>
                </tr>
            </tbody>
        @endif

    </table>

</body>

</html>
