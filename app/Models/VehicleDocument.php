<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleDocument extends Model
{
    use HasFactory, HasUuids, Searchable, SoftDeletes;

    public function type_document()
    {
        return $this->hasOne(TypeDocument::class, 'id', 'type_document_id');
    }
}
