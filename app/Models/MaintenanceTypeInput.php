<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceTypeInput extends Model
{
    use HasUuids, SoftDeletes;

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
}
