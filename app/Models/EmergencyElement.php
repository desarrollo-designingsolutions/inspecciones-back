<?php

namespace App\Models;

use App\Traits\Cacheable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmergencyElement extends Model
{
    use Cacheable, HasFactory, HasUuids,Searchable, SoftDeletes;

    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    protected $customCachePrefixes = [
        'string:{table}_list*',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function vehicleEmergencyElements()
    {
        return $this->hasMany(VehicleEmergencyElement::class, 'emergency_element_id');
    }
}
