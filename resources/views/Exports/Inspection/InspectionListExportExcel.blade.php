<div>
    <table>
        <thead>
            <tr>
                <th>Fecha de inspección</th>
                <th>Placa</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Tipo de inspección</th>
                <th>Inspector</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr class="">
                    <td>{{ $row['inspection_date'] }}</td>
                    <td>{{ $row['vehicle_license_plate'] }}</td>
                    <td>{{ $row['vehicle_brand_name'] }}</td>
                    <td>{{ $row['vehicle_model'] }}</td>
                    <td>{{ $row['inspection_type_name'] }}</td>
                    <td>{{ $row['user_full_name'] }}</td>
                    <td>{{ $row['is_active'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
