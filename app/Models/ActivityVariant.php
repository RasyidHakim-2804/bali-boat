<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityVariant extends Model
{
    use HasFactory, SoftDeletes;

    public const PASSENGER_TYPE = [
        'adult_local',
        'adult_foreign',
        'child_local',
        'child_foreign',
    ];
}
