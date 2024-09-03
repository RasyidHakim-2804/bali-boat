<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    use HasFactory;

    public const TYPE = [
        'activity', 'private-boat'
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(TagTranslation::class, 'tag_id', 'id');
    }

    public function privateBoats(): BelongsToMany
    {
        return $this->belongsToMany(PrivateBoat::class, 'pivot_private_boat_tags', 'tag_id', 'private_boat_id');
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'pivot_activity_tags', 'tag_id', 'activity_id');
    }
}
