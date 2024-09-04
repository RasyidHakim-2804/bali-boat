<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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

    public function scopeSearch($query, $search)
    {
        $query->addSelect('*',DB::raw(
            "( select translation from tag_translations " .
            "where tag_id = product_tags.id " .
            "and field = 'description' " .
            "and language = 'en' " .
            "order by id asc limit 1) as name"
        ))->having('name', 'LIKE','%' . $search . '%');
    }
}
