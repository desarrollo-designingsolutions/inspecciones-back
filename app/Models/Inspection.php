<?php

namespace App\Models;

use App\Traits\Cacheable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspection extends Model
{
    use Cacheable, HasUuids, Searchable, SoftDeletes;

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

    public function brand_vehicle()
    {
        return $this->hasOneThrough(
            BrandVehicle::class,    // Target model
            Vehicle::class,         // Intermediate model
            'id',                   // Foreign key on Vehicle table
            'id',                   // Foreign key on BrandVehicle table
            'vehicle_id',           // Local key on Inspection table
            'brand_vehicle_id'      // Local key on Vehicle table
        );
    }

    public function inspection_group_inspection()
    {
        return $this->belongsToMany(InspectionTypeGroup::class, 'inspection_group_inspections', 'inspection_id', 'inspection_type_group_id');
    }
}
