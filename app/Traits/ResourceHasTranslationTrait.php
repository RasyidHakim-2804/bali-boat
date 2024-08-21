<?php

namespace App\Traits;

use App\Models\Language;

trait ResourceHasTranslationTrait
{
    protected function separateByLanguage($resourceClass, string $relation, bool $isCollection = true)
    {
        return $this->whenLoaded($relation, function() use ($resourceClass, $isCollection, $relation) {
            $resource = [];
            
            foreach (Language::code() as $lang) {

                if ($isCollection) {
                    
                    $collection = $this->$relation->where('language', $lang);
                    
                    $resource[$lang] = $resourceClass::collection($collection);

                } else {

                    $data = $this->$relation->where('language', $lang)->first();

                    $resource[$lang] = new $resourceClass($data);
                }
            }

            return $resource;
        });
    }
}