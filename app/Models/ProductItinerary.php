<?php

namespace App\Models;

use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class ProductItinerary extends Model
{
    use HasFactory, ImageStorageTrait;

    protected $imageSetting = [
        'folder' => 'itineraries',
        'field'  => [
            'image',
        ],
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(ItineraryTranslation::class, 'itinerary_id', 'id');
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'pivot_activity_itineraries', 'itinerary_id', 'activity_id');
    }

    public function scopeSearch($query, $search)
    {
        $query->addSelect('*',DB::raw(
            "( select translation from itinerary_translations " .
            "where itinerary_id = product_itineraries.id " .
            "and field = 'title' " .
            "and language = 'en' " .
            "order by id asc limit 1) as title"
        ))->having('title', 'LIKE','%' . $search . '%');
    }
}
