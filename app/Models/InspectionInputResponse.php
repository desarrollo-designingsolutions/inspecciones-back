<?php

namespace App\Models;

use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionInputResponse extends Model
{
    use Cacheable, HasUuids, SoftDeletes;

    protected $guarded = [];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function inspectionTypeInput(): BelongsTo
    {
        return $this->belongsTo(InspectionTypeInput::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
