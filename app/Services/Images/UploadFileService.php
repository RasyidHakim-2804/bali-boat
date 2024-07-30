<?php

namespace App\Services\Images;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UploadFileService
{
    protected $file;
    
    protected $storage;

    protected $filePath;

    protected $fileName;

    protected $defaultExtention;

    public function __construct(string $filePath, object $file = null, string $storageDisk = 'public_upload')
    {
        $this->storage    = Storage::disk($storageDisk);
        $this->filePath   = $filePath;

        if ($file) $this->build($file);
    }

    protected function build($file)
    {
        $this->validateFile($file);
    
        $this->getDefaultExtention($file);

        $this->generateFileName();
    }

    protected function getDefaultExtention($file)
    {
        $this->defaultExtention = $file->getClientOriginalExtension();

        return $this->defaultExtention;
    }

    protected function generatefileName($extention = null, $additionalName = null)
    {
        $this->fileName = uniqid() . '.' . $this->defaultExtention;
            
        return $this;
    }

    protected function validateFile($file)
    {
        $fileExtention    = $file->getClientOriginalExtension();
        $allowedExtention = ['pdf'];

        if (!in_array($fileExtention, $allowedExtention)) {
            return abort(500, 'File type is not allowed !');
        }

        $this->file = $file;

        return $this;
    }

    protected function validateFolderPath()
    {
        $folders = explode('/', $this->filePath);

        if (!$this->storage->exists($folders[0])) {
            $this->storage->makeDirectory($folders[0], 0775);
        }
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function save($file = null)
    {
        if ($file) $this->build($file);

        $this->storage->put($this->filePath . '/' . $this->fileName, File::get($this->file));
    }

    public function delete(string $fileName)
    {
        $extentions = ['', '.pdf', '.PDF'];
        
        if ($this->storage->exists($this->filePath)) {
            foreach ($extentions as $key => $extention) {
                $file = $fileName . $extention;

                if ($this->storage->exists($this->filePath . '/' . $file)) {
                    $this->storage->delete($this->filePath . '/' . $file);
                }
            }
        }
    }
}