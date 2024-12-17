<?php

namespace App\Models;

use App\Traits\IsTranslationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateBoatTranslation extends Model
{
    use HasFactory, IsTranslationTrait;

    public function rental(): BelongsTo
    {
        return $this->belongsTo(PrivateBoat::class);
    }
}
