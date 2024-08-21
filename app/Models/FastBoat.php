<?php

namespace App\Models;

use App\Contracts\Models\CanUploadImage;
use App\Observers\AdminActionObserver;
use App\Traits\ImageStorageTrait;
use App\Traits\Models\HasImage;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

#[ObservedBy([AdminActionObserver::class])]
class FastBoat extends Model
{
    use HasFactory, ImageStorageTrait, HasSlug;

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
                ->saveSlugsTo('slug')
                ->startSlugSuffixFrom(2);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(FastBoatSchedule::class, 'fast_boat_id', 'id');
    }

    public function topSchedules(): HasMany
    {
        return $this->hasMany(FastBoatSchedule::class, 'fast_boat_id', 'id')->limit(3);
    }

    public function scopeActive(Builder $query, bool $active = true)
    {
        $query->where('active', '=', $active);
    }

}
