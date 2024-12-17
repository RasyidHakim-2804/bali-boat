<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    public function activities():HasMany
    {
        return $this->hasMany(Activity::class, 'location_id', 'id');
    }

    public function scopeSearch( $query, $search)
    {
        return $query->where('name', 'LIKE','%' . $search . '%');
    }
}
