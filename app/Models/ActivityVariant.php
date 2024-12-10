<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityVariant extends Model
{
    use HasFactory, SoftDeletes;

    public const PASSENGER_TYPE = [
        'local','foreign',
    ];

    public const NAME = [
        'adult', 'child'
    ];

    public function scopeType(Builder $query, $passengerType, $name)
    {
        $query->where('passenger_type', $passengerType)->where('name', $name);
    }
}
