<?php

namespace App\Models;

use App\Traits\IsTranslationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncludeTranslation extends Model
{
    use HasFactory, IsTranslationTrait;
}
