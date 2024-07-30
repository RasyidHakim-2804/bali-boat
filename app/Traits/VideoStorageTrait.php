<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait VideoStorageTrait
{
    protected $videoPath = 'videos';

    public function videoStorage()
    {
        return Storage::disk($this->videoStorageDisk());
    }

    public function videoUrl(string $videoField = null, string $videoExtention = null)
    {
        $video = $this->determinedvideoField();

        if ($videoField) {
            $video = $this->$videoField ?? null;
        }

        if (!$video) return '';

        if (filter_var($video, FILTER_VALIDATE_URL)) {
            return $video;
        }

        if ($videoExtention) {
            $video .= '.' . $videoExtention;
        }
        
        return $this->videoStorage()->url($this->videoPath() . '/' . $video);
    }

    public function videoPath()
    {
        return $this->videoPath . '/' . $this->determinedvideoPath();
    }

    public function videoField()
    {
        return $this->videoSetting['field'];
    }

    protected function determinedvideoField()
    {
        if ($this->videoSetting) {
            
            $video = $this->videoSetting['field'] ?? '';

            if (is_array($video)) return '';
            
            return $this->attributes[$video];
        }
    }

    protected function determinedvideoPath()
    {
        if ($this->videoSetting) {
            return $this->videoSetting['folder'] ?? 'public_upload';
        }
    }

    public function videoStorageDisk()
    {
        if ($this->videoSetting) {
            return $this->videoSetting['disk'] ?? 'public_upload';
        }
    }
}

