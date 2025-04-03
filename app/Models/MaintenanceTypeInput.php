<?php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceTypeInput extends Model
{
    use HasUuids, SoftDeletes,Cacheable;

    protected $casts = [
        'order' => 'integer',
    ];


    public function maintenanceTypeGroup(): BelongsTo
    {
        return $this->belongsTo(MaintenanceTypeGroup::class);
    }

    public function maintenanceInputResponses(): HasMany
    {
        return $this->hasMany(MaintenanceTypeInputResponse::class);
    }

    public function inspection_group_vehicle()
    {
        return $this->belongsToMany(Vehicle::class, 'inspection_group_vehicles', 'inspection_type_input_id', 'vehicle_id');
    }
}
