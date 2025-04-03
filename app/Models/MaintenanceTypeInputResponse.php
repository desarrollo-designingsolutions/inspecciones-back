<?php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceTypeInputResponse extends Model
{
    use Cacheable, HasUuids,SoftDeletes;

    protected $guarded = [];

    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(Maintenance::class);
    }

    public function maintenanceTypeInput(): BelongsTo
    {
        return $this->belongsTo(MaintenanceTypeInput::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
