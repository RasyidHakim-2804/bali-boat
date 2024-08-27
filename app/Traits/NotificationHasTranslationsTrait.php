<?php 

namespace App\Traits;

trait NotificationHasTranslationsTrait
{
    protected function getTranslations(string $translation, ?string $language = null)
    {
        if ($language == null) $language = app()->getLocale();

        return $this->translations[$language];
    }
}