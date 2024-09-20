<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaqCategory extends Model
{
    use HasFactory;

    public function translations(): HasMany
    {
        return $this->hasMany(FaqCategoryTranslation::class, 'faq_category_id', 'id');
    }

    public function faq(): HasMany
    {
        return $this->hasMany(Faq::class, 'category_id', 'id');
    }
}
