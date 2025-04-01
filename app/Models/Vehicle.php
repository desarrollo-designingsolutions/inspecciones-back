<?php

namespace App\Models;

use App\Traits\Cacheable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, HasUuids, Searchable, SoftDeletes, Cacheable;

    protected $casts = [
        'is_active' => 'boolean',
        'have_trailer' => 'boolean',
        'country_id' => 'integer',
        'state_id' => 'integer',
        'city_id' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function type_vehicle()
    {
        return $this->hasOne(TypeVehicle::class, 'id', 'type_vehicle_id');
    }

    public function brand_vehicle()
    {
        return $this->hasOne(BrandVehicle::class, 'id', 'brand_vehicle_id');
    }

    public function city()
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    public function state()
    {
        return $this->hasOne(State::class, 'id', 'state_id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function vehicle_structure()
    {
        return $this->hasOne(VehicleStructure::class, 'id', 'vehicle_structure_id');
    }

    public function type_documents()
    {
        return $this->hasMany(VehicleDocument::class, 'vehicle_id', 'id');
    }

    public function emergency_elements()
    {
        return $this->hasMany(VehicleEmergencyElement::class, 'vehicle_id', 'id');
    }

    public function inspection()
    {
        return $this->hasMany(Inspection::class, 'vehicle_id', 'id');
    }

    public function maintenance()
    {
        return $this->hasMany(Maintenance::class, 'vehicle_id', 'id');
    }

    public function inspection_group_vehicle()
    {
        return $this->belongsToMany(InspectionTypeGroup::class, 'inspection_group_vehicles', 'vehicle_id', 'inspection_type_group_id');
    }
}
