<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mantenimientos por mes</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 10px;
      margin: 5px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 3px;
    }

    td,
    th {
      border: 1px solid black;
      padding: 1px 2px;
      text-align: left;
    }

    th {
      text-align: center;
    }

    .group-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 5px;
    }

    .group-table td {
      border: 1px solid black;
      padding: 1px 2px;
      text-align: left;
      font-size: 7px;
      line-height: 1.2;
    }

    .group-header {
      background-color: #00BFFF;
      text-align: center;
      font-weight: bold;
      color: white;
      padding: 2px;
      font-size: 8px;
    }

    .subheader {
      text-align: center;
      font-weight: bold;
      font-size: 7px;
      padding: 1px;
    }

    .header-table {
      font-size: 9px;
    }

    .info-header {
      font-size: 8px;
    }

    img {
      max-width: 80px;
      max-height: 35px;
    }
  </style>
</head>

<body>
  <table class="header-table">
    <thead>
      <tr>
        <th colspan="2" style="width: 20%; text-align: center;">
          <img src="{{ public_path('storage/logo_ochoa.png') }}" alt="logo">
        </th>
        <th colspan="5" style="width: 80%; text-align: center; font-size: 10px;">
          SISTEMA INTEGRADO DE GESTION HSEQ
          <br>
          REPORTE DE MANTENIMIENTO DE VEHICULOS
        </th>
      </tr>
    </thead>
  </table>
  <table class="info-header">
    <thead>
      <tr>
        <td colspan="6">Codigo: GO-F-01</td>
        <td colspan="6">Fecha: <b>{{ $data['maintenance_date'] }}</b></td>
        <td colspan="6">Versión 12</td>
        <td colspan="6">Pagina 1 de 1</td>
      </tr>
      <tr>
        <td colspan="6" rowspan="2">Ciudad: <b>{{ $data['city'] }}</b></td>
        <td colspan="6" rowspan="2">Fecha:</td>
        <td colspan="6" rowspan="2">Realizado: {{ $data['operator']['name'] }}</td>
        <td colspan="6">Placa: {{ $data['vehicle']['plate'] }}</td>
      </tr>
      <tr>
        <td colspan="6">Km: {{ $data['vehicle']['current_mileage'] }}</td>
      </tr>
    </thead>
  </table>

  <table class="info-table">
    <thead>
      <tr>
        <th colspan="2">Inspección</th>
        <th>Tipo de respuesta</th>
        <th>Tipo de mantenimiento</th>
        <th colspan="3">Comentario</th>
      </tr>
    </thead>
    <tbody>
      @foreach($data['groups'] as $group)
      {{-- Fila título del grupo por encima del encabezado --}}
      <tr>
        <td colspan="7" style="text-align: center;">
          {{ $group['group_name'] }}
        </td>
      </tr>

      {{-- Encabezado por grupo --}}
      <tr>
        <td colspan="2">Inspección</td>
        <td>Tipo de respuesta</td>
        <td>Tipo de mantenimiento</td>
        <td colspan="3">Comentario</td>
      </tr>

      {{-- Filas de inspecciones --}}
      @foreach($group['inputs'] as $input)
      <tr>
        <td colspan="2">{{ $input['input_name'] }}</td>
        <td>{{ $input['response_type'] ?? '' }}</td>
        <td>{{ $input['response_type_maintenance'] ?? '' }}</td>
        <td colspan="3">{{ $input['response_comment'] ?? '' }}</td>
      </tr>
      @endforeach
      @endforeach
    </tbody>
  </table>

  <table class="info-header">
    <thead>
      <tr>
        <td>Convenciones: INSP: Inspección - MANT: Mantenimiento - Preventivo: P - Correctivo: C</td>
      </tr>
      <tr>
        <td style="text-align: center;">OBSERVACIONES GENERALES O TRABAJOS PENDIENTES</td>
      </tr>
      <tr>
        <td>{{$data['general_comment']}}</td>
      </tr>
    </thead>
  </table>
</body>

</html>