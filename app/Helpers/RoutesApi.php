<?php

namespace App\Helpers;

class RoutesApi
{
    // esto es para las apis que no requieran auth
    public const ROUTES_API = [
        'routes/api.php',
    ];

    // esto es para las apis que si requieran auth
    public const ROUTES_AUTH_API = [
        'routes/query.php',
        'routes/company.php',
        'routes/user.php',
        'routes/role.php',
        'routes/client.php',
        'routes/typeVehicle.php',
        'routes/brandVehicle.php',
        'routes/typeDocument.php',
        'routes/emergencyElement.php',
        'routes/vehicle.php',
        'routes/inspection.php',
        'routes/maintenance.php',
    ];
}
