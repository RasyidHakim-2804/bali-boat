<?php

namespace App\Models;

use App\Traits\IsTranslationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqTranslation extends Model
{
    use HasFactory, IsTranslationTrait;

    public function faq()
    {
        return $this->belongsTo(Faq::class, 'faq_id', 'id');
    }
}
