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
        <td colspan="6">Fecha: 13/01/2023</td>
        <td colspan="6">Versión 12</td>
        <td colspan="6">Pagina 1 de 1</td>
      </tr>
      <tr>
        <td colspan="6" rowspan="2">Ciudad de mantenimiento: {{ $data['city'] }}</td>
        <td colspan="6" rowspan="2">Fecha: {{ $data['maintenance_date'] }}</td>
        <td colspan="6" rowspan="2">Realizado por: {{ $data['operator']['name'] }}</td>
        <td colspan="6">Placa: {{ $data['vehicle']['plate'] }}</td>
      </tr>
      <tr>
        <td colspan="6">Kilometraje: {{ $data['vehicle']['current_mileage'] }}</td>
      </tr>
    </thead>
  </table>

  @php
  // Dividir grupos en dos arrays: pares e impares
  $leftGroups = [];
  $rightGroups = [];

  foreach($data['groups'] as $index => $group) {
  if($index % 2 == 0) {
  $leftGroups[] = $group;
  } else {
  $rightGroups[] = $group;
  }
  }

  // Obtener el máximo de grupos entre las dos columnas
  $maxGroups = max(count($leftGroups), count($rightGroups));
  @endphp

  <!-- Tabla principal con filas para cada par de grupos -->
  <table style="width: 100%; border-collapse: collapse; border: none;">
    @for($i = 0; $i < $maxGroups; $i++)
      @php
      // Calcular el máximo de inputs ANTES de generar las columnas
      $leftInputCount=isset($leftGroups[$i]) ? count($leftGroups[$i]['inputs']) : 0;
      $rightInputCount=isset($rightGroups[$i]) ? count($rightGroups[$i]['inputs']) : 0;
      $maxInputs=max($leftInputCount, $rightInputCount);
      @endphp

      <tr>
      <!-- COLUMNA IZQUIERDA -->
      <td style="width: 50%; vertical-align: top; border: none; padding: 0 2px;">
        @if(isset($leftGroups[$i]))
        <table class="group-table">
          <tr>
            <td colspan="6" class="group-header" style="text-align: center;">
             {{ strtoupper($rightGroups[$i]['group_name']) }}
            </td>
          </tr>
          <tr>
            <td colspan="2" class="subheader" style="text-align: center;">ITEM</td>
            <td colspan="2" class="subheader" style="text-align: center;">OBSERVACIONES</td>
            <td class="subheader" style="text-align: center;">INSP</td>
            <td class="subheader" style="text-align: center;">MANT</td>
          </tr>

          @foreach($leftGroups[$i]['inputs'] as $input)
          <tr>
            <td colspan="2">{{ $input['input_name'] }}</td>
            <td colspan="2">{{ $input['response_comment'] ?? '' }}</td>
            <td style="text-align: center;">{{ $input['response_type'] ? 'X' : '' }}</td>
            <td style="text-align: center;">{{ $input['response_type_maintenance'] ? $input['response_type_maintenance'] === 'Correctivo' ? 'C' : 'P' : '' }}</td>
          </tr>
          @endforeach

          {{-- Rellenar con filas vacías si faltan --}}
          @for($j = $leftInputCount; $j < $maxInputs; $j++)
            <tr>
            <td colspan="2">&nbsp;</td>
            <td colspan="2">&nbsp;</td>
            <td style="text-align: center;">&nbsp;</td>
            <td style="text-align: center;">&nbsp;</td>
            </tr>
            @endfor
        </table>
        @endif
      </td>

      <!-- COLUMNA DERECHA -->
      <td style="width: 50%; vertical-align: top; border: none; padding: 0 2px;">
        @if(isset($rightGroups[$i]))
        <table class="group-table">
          <tr>
            <td colspan="6" class="group-header" style="text-align: center;">
             {{ strtoupper($rightGroups[$i]['group_name']) }}
            </td>
          </tr>
          <tr>
            <td colspan="2" class="subheader" style="text-align: center;">ITEM</td>
            <td colspan="2" class="subheader" style="text-align: center;">OBSERVACIONES</td>
            <td class="subheader" style="text-align: center;">INSP</td>
            <td class="subheader" style="text-align: center;">MANT</td>
          </tr>

          @foreach($rightGroups[$i]['inputs'] as $input)
          <tr>
            <td colspan="2">{{ $input['input_name'] }}</td>
            <td colspan="2">{{ $input['response_comment'] ?? '' }}</td>
            <td style="text-align: center;">{{ $input['response_type'] ? 'X' : '' }}</td>
            <td style="text-align: center;">{{ $input['response_type_maintenance'] ? $input['response_type_maintenance'] === 'Correctivo' ? 'C' : 'P' : '' }}</td>
          </tr>
          @endforeach

          {{-- Rellenar con filas vacías si faltan --}}
          @for($j = $rightInputCount; $j < $maxInputs; $j++)
            <tr>
            <td colspan="2">&nbsp;</td>
            <td colspan="2">&nbsp;</td>
            <td style="text-align: center;">&nbsp;</td>
            <td style="text-align: center;">&nbsp;</td>
            </tr>
            @endfor
        </table>
        @endif
      </td>
      </tr>
      @endfor
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
