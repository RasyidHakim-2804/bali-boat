<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

trait HasImage
{
    private $defaultPath = 'upload/images/';
    
    public function getDirPath():string
    {
        if (isset($this->suffixPath)) {
            return $this->defaultPath . trim($this->suffixPath, '/') . '/';
        }

        return $this->defaultPath;
    }

    public function getDisk():string
    {
        return $this->disk;
    }

    /**
     * return path of image folder
     * this function for your model if already has image 
     */
    public function getOldImage(): string
    {
        if ( !isset( $this->attributes['image'])){
            return ' ';
        }  

        return $this->getDirPath() .  $this->attributes['image'];        
    }

    public function image(): Attribute
    {
        return new Attribute(
            get: function($value = null) {

                if ($value == null) {
                    
                    return null;
                    
                } elseif ((Str::contains($value, 'https://')) || Str::contains($value,'http://')) {
                    
                    return $value;

                } else {

                    return config('app.frontend_url') . 'storage/' . $this->getDirPath() . $value;
                }
                
            }
        );
    }

    /**
     * The model must have the following attributes:
     */

    /**
     * column name containing the image path
     * 
     * protected $imageColumn;
     */

    /**
     * column name containing the dir path to save the image
     * 
     * protected $suffixPath = 'products/';
     */

    /**
     * type of disk
     *
     * protected $disk = 'local';
     */

    /**
     * suffix for image, ex: .jpg, .png, .gif, etc.
     * 
     * protected $format               = '';
     */

}