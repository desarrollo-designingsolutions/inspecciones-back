<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceTypeGroup extends Model
{
    use HasUuids, SoftDeletes;

    protected $casts = [
        'order' => 'integer',
    ];

    public function maintenanceType(): BelongsTo
    {
        return $this->belongsTo(MaintenanceType::class);
    }

    public function maintenanceTypeInputs(): HasMany
    {
        return $this->hasMany(MaintenanceTypeInput::class);
    }
}
