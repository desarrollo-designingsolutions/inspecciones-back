<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspection extends Model
{
    use HasUuids, Searchable, SoftDeletes;

    protected $casts = [
        'order' => 'integer',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function inspectionType(): BelongsTo
    {
        return $this->belongsTo(InspectionType::class);
    }

    public function user_inspector(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function user_operator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function inspectionInputResponses(): HasMany
    {
        return $this->hasMany(InspectionInputResponse::class);
    }

    public function inspectionDocumentVerifications(): HasMany
    {
        return $this->hasMany(InspectionDocumentVerification::class);
    }
}
