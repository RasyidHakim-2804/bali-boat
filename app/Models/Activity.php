<?php

namespace App\Models;

use App\Observers\AdminActionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

#[ObservedBy([AdminActionObserver::class])]
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

    public function highlightTranslations(): HasMany
    {
        return $this->hasMany(ActivityTranslation::class, 'activity_id', 'id')->where('field', '=', 'highlight');
    }

    public function descriptionTranslations()
    {
        return $this->hasMany(ActivityTranslation::class, 'activity_id', 'id')->where('field', 'description');
    }

    public function includes(): HasMany
    {
        return $this->hasMany(ActivityInclude::class, 'activity_id', 'id');
    }

    public function excludes(): HasMany
    {
        return $this->hasMany(ActivityExclude::class, 'activity_id', 'id');
    }

    public function itinararies(): HasMany
    {
        return $this->hasMany(ActivityItinerary::class, 'activity_id', 'id');
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

    public function scopePerson($query, $person)
    {
        $query->where('max_person', '>=', $person);
    }

}
