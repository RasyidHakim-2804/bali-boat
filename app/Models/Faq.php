<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faq extends Model
{
    use HasFactory;

    public function translations() : HasMany
    {
        return $this->hasMany(FaqTranslation::class, 'faq_id', 'id');
    }
}
