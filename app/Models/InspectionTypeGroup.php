<?php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionTypeGroup extends Model
{
    use HasUuids, SoftDeletes,Cacheable;

    protected $casts = [
        'order' => 'integer',
    ];

    public function inspectionType(): BelongsTo
    {
        return $this->belongsTo(InspectionType::class);
    }

    public function inspectionTypeInputs(): HasMany
    {
        return $this->hasMany(InspectionTypeInput::class);
    }
}
