<?php

namespace App\Services\Images;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageService
{
    protected $storage;

    protected $imagePath;

    protected $imageName;

    protected $imagesName = [];

    protected $imageDefaultExtention;

    protected $imageBestResolution;

    protected $imageDriver;

    protected $image;

    protected $bestImageWidthPixel;

    protected $bestImageHeightPixel;

    public function __construct(string $imagePath, object $file = null, string $storageDisk = 'public_upload', string $imageDriver = 'gd')
    {
        $this->storage     = Storage::disk($storageDisk);
        $this->imagePath   = $imagePath;
        $this->imageDriver = $imageDriver;

        if ($file) $this->build($file);
    }

    protected function build($file)
    {
        ini_set('memory_limit','512M');

        $this->validateFile($file);
    
        $this->getImageDefaultExtention($file);

        $this->generateBestPixelPoint($file, 1000);

        $this->generateImage($file);

        $this->generateImageBestResolution($file);

        $this->generateImageName();

        $this->validateFolderPath();
    }

    protected function generateBestPixelPoint($file, $minimunPixel)
    {
        $getImagePixels     = getimagesize($file);
        $imageWidthPixel    = $getImagePixels[0];
        $imageHeightPixel   = $getImagePixels[1];

        while (($imageWidthPixel > $minimunPixel) || ($imageHeightPixel > $minimunPixel)) {
            $point = $imageWidthPixel / $minimunPixel;

            if ($imageHeightPixel > $minimunPixel) $point = $imageHeightPixel / $minimunPixel;

            $imageWidthPixel  = $imageWidthPixel / $point;
            $imageHeightPixel = $imageHeightPixel / $point;
        }
        $this->bestImageWidthPixel  = $imageWidthPixel;
        $this->bestImageHeightPixel = $imageHeightPixel;

        return $this;
    }

    protected function generateImage($file)
    {
        Image::configure(['driver' => $this->imageDriver]);

        if (($this->imageDefaultExtention == 'gif') && ($this->imageDriver == 'gd')) {
            Image::configure(['driver' => 'imagick']);
        }

        $this->image = Image::make($file)->resize($this->bestImageWidthPixel, $this->bestImageHeightPixel);

        $this->image->backup();

        return $this;
    }

    protected function getImageDefaultExtention($file)
    {
        $this->imageDefaultExtention = $file->getClientOriginalExtension();

        return $this->imageDefaultExtention;
    }

    protected function generateImageName($extention = null, $additionalName = null)
    {
        $this->imageName = uniqid() . '.' . $this->imageDefaultExtention;
            
        return $this;

        if (!$this->imageName) {
            $this->imageName = uniqid() . '.' . $this->imageDefaultExtention;
            
            return $this;
        }

        if ($extention) {
            $extention = '.' . $this->imageDefaultExtention;
        }

        if ($additionalName && !$extention) {
            $extention = $this->imageDefaultExtention;
        }

        return $this->imageName . $additionalName . $extention;
    }

    protected function validateFile($file)
    {
        $fileExtention    = $file->getClientOriginalExtension();
        $allowedExtention = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'JPG', 'JPEG', 'PNG'];

        if (!in_array($fileExtention, $allowedExtention)) {
            return abort(500, 'File is not an image !');
        }
    }

    protected function validateFolderPath()
    {
        $folders = explode('/', $this->imagePath);

        if (!$this->storage->exists($folders[0])) {
            $this->storage->makeDirectory($folders[0], 0775);
        }

        if (!$this->storage->exists($this->imagePath)) {
            $this->storage->makeDirectory($this->imagePath, 0775);
        }
    }

    protected function validateImage()
    {
        if (!$this->image) {
            return abort(500, 'Cannot proccess image service. Image is null !');
        }

        $this->image->reset();

        $this->image->backup();
    }

    protected function generateImageBestResolution($file)
    {
        $fileSize = $file->getSize() / 1000;

        if ($fileSize > 5000) {
            $this->imageBestResolution = 30;
        } elseif ($fileSize > 1000) {
            $this->imageBestResolution = 40;
        } elseif ($fileSize > 500) {
            $this->imageBestResolution = 60;
        } elseif ($fileSize > 0) {
            $this->imageBestResolution = 80;
        }

        return $this;
    }

    public function getImageName()
    {
        return $this->imageName;
    }

    public function save($file = null, $resolution = null)
    {
        ($file) ? $this->build($file) : $this->validateImage();

        $path       = $this->storage->path($this->imagePath . '/' . ($this->imageName));
        $resolution = $resolution ?? $this->imageBestResolution;
        
        $this->image->save($path, $resolution);
    }

    // public function saveWebp($resolution = null)
    // {
    //     $this->validateImage();

    //     $imageName  = $this->imageName . '.webp';
    //     $path       = $this->storage->path($this->imagePath . '/' . ($imageName));
    //     $resolution = $resolution ?? $this->imageBestResolution;
    //     $imageWebp  = $this->image->encode('webp', $resolution);
        
    //     $imageWebp->save($path, $resolution);
    // }

    public function delete(string $imageName)
    {
        $extentions = ['', '.png', '.jpg', '.jpeg', '.webp', '.gif', '.JPG', '.JPEG', '.PNG'];
        
        if ($this->storage->exists($this->imagePath)) {
            foreach ($extentions as $key => $extention) {
                $file = $imageName . $extention;

                if ($this->storage->exists($this->imagePath . '/' . $file)) {
                    $this->storage->delete($this->imagePath . '/' . $file);
                }
            }
        }
    }
}