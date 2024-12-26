<?php

namespace App\Models;


use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypePhotograph extends Model
{

    use HasFactory, HasUuids, Searchable,SoftDeletes;

    protected $casts = [
        'is_active' => 'boolean',
    ];
}