<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Model
{
    use HasUuids, Searchable, SoftDeletes;

    protected $casts = [
        'order' => 'integer',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function maintenanceType(): BelongsTo
    {
        return $this->belongsTo(MaintenanceType::class);
    }

    public function maintenanceTypeGroups(): HasMany
    {
        return $this->hasMany(MaintenanceTypeGroup::class);
    }

    public function user_mechanic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_mechanic_id', 'id');
    }
    public function user_operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_operator_id', 'id');
    }
    public function user_inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_inspector_id', 'id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function maintenanceInputResponses(): HasMany
    {
        return $this->hasMany(MaintenanceTypeInputResponse::class);
    }
}
