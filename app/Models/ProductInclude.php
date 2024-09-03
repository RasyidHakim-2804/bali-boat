<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductInclude extends Model
{
    use HasFactory;

    public const TYPE = [
        'activity', 'private-boat'
    ];

    public function privateBoats(): BelongsToMany
    {
        return $this->belongsToMany(PrivateBoat::class, 'pivot_private_boat_includes', 'include_id', 'private_boat_id');
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'pivot_activity_includes', 'include_id', 'activity_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(IncludeTranslation::class, 'include_id', 'id');
    }
}
