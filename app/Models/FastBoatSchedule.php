<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class FastBoatSchedule extends Model
{
    use HasFactory, HasEagerLimit, SoftDeletes;

    public function fastBoat(): BelongsTo
    {
        return $this->belongsTo(FastBoat::class);
    }

    public function departureHarbor(): BelongsTo
    {
        return $this->belongsTo(Harbor::class,'departure_harbor_id', 'id');
    }

    public function arrivalHarbor(): BelongsTo
    {
        return $this->belongsTo(Harbor::class,'arrival_harbor_id', 'id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(FastBoatScheduleVariant::class);
    }

    public function inactiveCalender(): HasMany
    {
        return $this->hasMany(InactiveFastBoatCalender::class, 'product_id', 'id');
    }

    // Scope Function

    public function scopeFromTo($query, $from, $to)
    {
        return $query->where('departure_harbor_id', '=', $from)
                      ->where('arrival_harbor_id', '=', $to);
    }

    public function scopeDepartureTime($query, $time = 'morning')
    {
        $operator = '<=';

        if ($time == 'afternoon') $operator = '>=';

        return $query->where('departure_time', $operator, '12:00');
    }

    public function scopeActive($query, bool $active = true)
    {
        $query->where('is_active', '=', $active);
    }

    public function scopeBoatActive($query, bool $active = true)
    {
        $query->whereHas('fastBoat', function($query) use ($active) {
            $query->where('is_active', '=', $active);
        });
    }

    public function scopeSelectVariantPrice($query)
    {
        $query->addSelect('*', DB::raw(
            '(select price from fast_boat_schedule_variants' .
            ' where fast_boat_schedule_id = fast_boat_schedules.id' .
            ' and age_type = "adult"' .
            ' and passenger_type = "local"' .
            '  limit 1) as variant_price'
        ));
    }

    public function scopeDate(Builder $query, $date)
    {
        $query->whereDoesntHave('inactiveCalender', function ($query) use ($date) {
            $query->where('date', $date);
        });
    }
}
