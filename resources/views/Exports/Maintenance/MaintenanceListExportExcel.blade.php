<div>
    <table>
        <thead>
            <tr>
                <th>Fecha de Mantenimiento</th>
                <th>Placa</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Inspector</th>
                <th>Asignado a</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
            <tr class="">
                <td>{{ $row['maintenance_date'] }}</td>
                <td>{{ $row['vehicle_license_plate'] }}</td>
                <td>{{ $row['vehicle_brand_name'] }}</td>
                <td>{{ $row['vehicle_model'] }}</td>
                <td>{{ $row['user_inspector_full_name'] }}</td>
                <td>{{ $row['user_mechanic_full_name'] }}</td>
                <td>{{ $row['status'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>