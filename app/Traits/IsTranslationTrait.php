<?php

namespace App\Traits;

trait IsTranslationTrait
{
    public function scopeField($query, string $field)
    {
        $query->where('field', '=', $field);
    }

    public function scopeLanguage($query, ?string $language = null)
    {
        if ($language) {
            $query->where('language', '=', $language);
        } else {
            $query->where('language', '=', app()->getLocale());
        }
        
    }
}