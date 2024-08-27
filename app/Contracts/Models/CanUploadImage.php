<?php
namespace App\Contracts\Models;

interface CanUploadImage
{
    public function getDirPath():string;
    public function getDisk():string;
    public function getOldImage(): ?string;
}