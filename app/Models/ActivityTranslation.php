<?php

namespace App\Models;

use App\Traits\IsTranslationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityTranslation extends Model
{
    use HasFactory, IsTranslationTrait;

    public const FIELD = [
        'highlight',
        'description',
        'meta',
    ];
}
