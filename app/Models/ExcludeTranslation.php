<?php

namespace App\Models;

use App\Traits\IsTranslationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcludeTranslation extends Model
{
    use HasFactory, IsTranslationTrait;
}
