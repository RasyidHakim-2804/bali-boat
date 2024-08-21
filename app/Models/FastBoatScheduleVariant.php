<?php

namespace App\Models;

use App\Observers\AdminActionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([AdminActionObserver::class])]
class FastBoatScheduleVariant extends Model
{
    use HasFactory;

    public const PASSENGER_TYPE =   [
        'local', 'foreign'
    ];

    public const AGE_TYPE =   [
        'adult', 'child'
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
