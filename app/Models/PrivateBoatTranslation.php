<?php

namespace App\Models;

use App\Observers\AdminActionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([AdminActionObserver::class])]
class PrivateBoatTranslation extends Model
{
    use HasFactory;

    public function rental(): BelongsTo
    {
        return $this->belongsTo(PrivateBoat::class);
    }

    public function scopeLanguage($query, $lang)
    {
        $query->where('language',  $lang);
    }

    public function scopeField($query, $field)
    {
        $query->where('field',  $field);
    }
}
