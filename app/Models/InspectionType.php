<?php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionType extends Model
{
    use Cacheable,HasUuids, SoftDeletes;

    protected $casts = [
        'order' => 'integer',
    ];

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    public function inspectionTypeGroups(): HasMany
    {
        return $this->hasMany(InspectionTypeGroup::class);
    }
}
