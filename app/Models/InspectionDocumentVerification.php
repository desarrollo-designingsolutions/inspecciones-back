<?php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionDocumentVerification extends Model
{
    use HasUuids, Cacheable;

    protected $guarded = [];

    protected $casts = [
        'original' => 'boolean',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function vehicleDocument(): BelongsTo
    {
        return $this->belongsTo(VehicleDocument::class);
    }
}
