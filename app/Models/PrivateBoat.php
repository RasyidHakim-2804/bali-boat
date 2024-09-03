<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class PrivateBoat extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

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

    public function includes(): BelongsToMany
    {
        return $this->belongsToMany(ProductInclude::class, 'pivot_private_boat_includes', 'private_boat_id', 'include_id')
                    ->withPivot(['position']);
    }

    public function specification(): HasOne
    {
        return $this->hasOne(PrivateBoatSpecification::class, 'private_boat_id', 'id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PrivateBoatImage::class);
    }

    public function thumbnailImage(): HasOne
    {
        return $this->hasOne(PrivateBoatImage::class)->ofMany('position', 'min');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(PrivateBoatVariant::class, 'private_boat_id', 'id')->where('is_default', '=', 0);
    }

    public function defaultVariant(): HasOne
    {
        return $this->hasOne(PrivateBoatVariant::class, 'private_boat_id', 'id')->where('is_default', '=', 1);
    }

    public function thumbnailVariant()
    {
        return $this->hasOne(PrivateBoatVariant::class, 'private_boat_id', 'id')->ofMany('id', 'MIN');
    }

    public function scopeSelectVariantPrice($query)
    {
        $query->addSelect('*',DB::raw(
            "(select price from private_boat_variants where private_boat_id = private_boats.id order by price asc limit 1) as price"
        ));
    }

    public function scopeActive($query, bool $active = true)
    {
        $query->where('is_active', '=', $active);
    }

}
