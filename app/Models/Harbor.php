<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Harbor extends Model
{
    use HasFactory;

    public function scopeSearch( $query, $search)
    {
        return $query->where('name', 'LIKE','%' . $search . '%');
    }

    public function departureSchedules()
    {
        return $this->hasMany(FastBoatSchedule::class, 'departure_harbor_id')->withTrashed();
    }

    public function arrivalSchedules()
    {
        return $this->hasMany(FastBoatSchedule::class, 'arrival_harbor_id')->withTrashed();
    }
}
