<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait ImageStorageTrait
{
    protected $imagePath = 'images';

    public function imageStorage()
    {
        return Storage::disk($this->imageStorageDisk());
    }

    public function imageUrl(string $imageField = null, string $imageExtention = null)
    {
        $image = $this->determinedImageField();

        if ($imageField) $image = $this->$imageField ?? null;

        if (!$image) return '';

        if (filter_var($image, FILTER_VALIDATE_URL)) return $image;

        if ($imageExtention) $image .= '.' . $imageExtention;
        
        return $this->imageStorage()->url($this->imagePath() . '/' . $image);
    }

    public function imagePath()
    {
        return $this->imagePath . '/' . $this->determinedImagePath();
    }

    public function imageField()
    {
        return $this->imageSetting['field'];
    }

    protected function determinedImageField()
    {
        if ($this->imageSetting) {
            
            $image = $this->imageSetting['field'] ?? '';

            if (is_array($image)) return '';
            
            return $this->attributes[$image];
        }
    }

    protected function determinedImagePath()
    {
        if ($this->imageSetting) {
            return $this->imageSetting['folder'] ?? 'public_upload';
        }
    }

    public function imageStorageDisk()
    {
        if ($this->imageSetting) {
            return $this->imageSetting['disk'] ?? 'public_upload';
        }
    }
}

