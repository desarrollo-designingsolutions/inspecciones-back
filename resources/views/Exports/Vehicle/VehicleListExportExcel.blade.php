<div>
    <table>
        <thead>
            <tr>
                <th>Placa</th>
                <th>Clase de vehículo</th>
                <th>Fecha de matrícula</th>
                <th>Modelo</th>
                <th>Ciudad de operación</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr class="">
                    <td>{{ $row['license_plate'] }}</td>
                    <td>{{ $row['type_vehicle_name'] }}</td>
                    <td>{{ $row['date_registration'] }}</td>
                    <td>{{ $row['model'] }}</td>
                    <td>{{ $row['city_name'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
