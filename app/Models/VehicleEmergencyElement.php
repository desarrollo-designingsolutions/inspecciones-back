<?php

namespace App\Models;

use App\Traits\Cacheable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleEmergencyElement extends Model
{
    use Cacheable, HasFactory, HasUuids, Searchable,SoftDeletes;

    public function emergency_element()
    {
        return $this->hasOne(EmergencyElement::class, 'id', 'emergency_element_id');
    }
}
