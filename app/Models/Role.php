<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;
    use HasUuids;

    protected $primaryKey = 'id';

    protected $casts = [
        'operator' => 'boolean',
        'mechanic' => 'boolean',
        'inspector' => 'boolean',
    ];

    public function allUsers()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
