<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FastBoatScheduleVariant extends Model
{
    use HasFactory, SoftDeletes;

    public const PASSENGER_TYPE =   [
        'local', 'foreign', 'itas'
    ];

    public const AGE_TYPE =   [
        'adult', 'child', 'infant'
    ];

    public function scopeType(Builder $query, $passengerType, $ageType)
    {
        $query->where('passenger_type', $passengerType)->where('age_type', $ageType);
    }

    public function schedule():BelongsTo
    {
        return $this->belongsTo(FastBoatSchedule::class);
    }
}
