<?php

namespace App\Models;

use App\Observers\AdminActionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([AdminActionObserver::class])]
class ActivityItinerary extends Model
{
    use HasFactory;

    public const TYPE = [
        'start',
        'boat',
        'car',
        'location'
    ];

    public function scopeLanguage($query, string $lang)
    {
        $query->where('language', '=', $lang);
    }
}
