<?php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceType extends Model
{
    use Cacheable,HasUuids,SoftDeletes;

    protected $casts = [
        'order' => 'integer',
    ];

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function maintenanceTypeGroups(): HasMany
    {
        return $this->hasMany(MaintenanceTypeGroup::class);
    }
}
