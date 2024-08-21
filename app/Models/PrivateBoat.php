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
class PrivateBoat extends Model
{
    use HasFactory, HasSlug;

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                ->generateSlugsFrom('name')
                ->saveSlugsTo('slug');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'pivot_private_boat_tags', 'private_boat_id', 'tag_id');
    }

    public function pickupHarbor(): BelongsTo
    {
        return $this->belongsTo(Harbor::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PrivateBoatTranslation::class);
    }

    public function highlightTranslations(): HasMany
    {
        return $this->hasMany(PrivateBoatTranslation::class, 'private_boat_id', 'id')->where('field', '=', 'highlight');
    }

    public function metaTranslations()
    {
        return $this->hasMany(PrivateBoatTranslation::class, 'private_boat_id', 'id')->where('field', 'meta');
    }

    public function includes(): HasMany
    {
        return $this->hasMany(PrivateBoatInclude::class, 'private_boat_id', 'id');
    }

    public function specifications(): HasMany
    {
        return $this->hasMany(PrivateBoatSpecification::class, 'private_boat_id', 'id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PrivateBoatImage::class);
    }

    public function thumbnailImage(): HasOne
    {
        return $this->hasOne(PrivateBoatImage::class)->ofMany('position', 'min');
    }

    public function defaultVariant(): HasOne
    {
        return $this->hasOne(PrivateBoatVariant::class);
    }

    public function scopeSelectVariantPrice($query)
    {
        $query->addSelect('*',DB::raw(
            "(select price from private_boat_variants where private_boat_id = private_boats.id order by price asc limit 1) as price"
        ));
    }
}
