<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Activity extends Model
{
    use HasFactory, HasSlug;

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
        ->generateSlugsFrom('name')
        ->saveSlugsTo('slug');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'pivot_activity_tags', 'activity_id', 'tag_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ActivityTranslation::class);
    }

    public function includes(): BelongsToMany
    {
        return $this->belongsToMany(ProductInclude::class, 'pivot_activity_includes', 'activity_id', 'include_id');
    }

    public function excludes(): BelongsToMany
    {
        return $this->belongsToMany(ProductExclude::class, 'pivot_activity_excludes', 'activity_id', 'exclude_id');
    }

    public function itineraries(): BelongsToMany
    {
        return $this->belongsToMany(ProductItinerary::class, 'pivot_activity_itineraries', 'activity_id', 'itinerary_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ActivityImage::class);
    }

    public function thumbnailImage(): HasOne
    {
        return $this->hasOne(ActivityImage::class)->ofMany('position', 'min');
    }

    public function defaultVariant(): HasOne
    {
        return $this->hasOne(ActivityVariant::class);
    }

    public function scopeSelectVariantPrice($query)
    {
        $query->addSelect('*',DB::raw(
            '(select price from activity_variants where activity_id = activities.id order by price asc limit 1) as price'
        ));
    }

    public function scopePeopleCount($query, $person)
    {
        $query->where('max_person', '>=', $person);
    }

    public function scopeActive($query, bool $active = true)
    {
        $query->where('is_active', '=', $active);
    }

}
