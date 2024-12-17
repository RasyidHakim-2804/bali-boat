<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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

    public function scopeSearch($query, $search)
    {
        $query->addSelect('*',DB::raw(
            "( select translation from include_translations " .
            "where include_id = product_includes.id " .
            "and field = 'description' " .
            "and language = 'en' " .
            "order by id asc limit 1) as description"
        ))->having('description', 'LIKE','%' . $search . '%');
    }
}
