<div>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr class="">
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['is_active'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
