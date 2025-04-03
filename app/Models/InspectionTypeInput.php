<?php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionTypeInput extends Model
{
    use HasUuids, SoftDeletes,Cacheable;

    protected $casts = [
        'order' => 'integer',
    ];

    public function inspectionTypeGroup(): BelongsTo
    {
        return $this->belongsTo(InspectionTypeGroup::class);
    }

    public function inspectionInputResponses(): HasMany
    {
        return $this->hasMany(InspectionInputResponse::class);
    }
}
