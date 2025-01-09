<div>
    <table>
        <thead>
            <tr>
                <th>Placa</th>
                <th>Clase de vehículo</th>
                <th>Fecha de matrícula</th>
                <th>Operador</th>
                <th>Modelo</th>
                <th>Ciudad de operación</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr class="">
                    <td>{{ $row['license_plate'] }}</td>
                    <td>{{ $row['type_vehicle_name'] }}</td>
                    <td>{{ $row['date_registration'] }}</td>
                    <td>{{ $row['operator'] }}</td>
                    <td>{{ $row['model'] }}</td>
                    <td>{{ $row['city_name'] }}</td>
                    <td>{{ $row['is_active'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
