<?php

namespace App\Models;

use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class FastBoat extends Model
{
    use HasFactory, ImageStorageTrait, HasSlug, HasEagerLimit, SoftDeletes;

    protected $imageSetting = [
        'folder' => 'fast_boats',
        'field'  => [
            'image',
        ],
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                ->generateSlugsFrom('name')
                ->saveSlugsTo('slug');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(FastBoatSchedule::class, 'fast_boat_id', 'id');
    }

    public function scopeActive(Builder $query, bool $active = true)
    {
        $query->where('is_active', '=', $active);
    }

}
