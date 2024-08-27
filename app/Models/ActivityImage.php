<?php

namespace App\Models;

use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityImage extends Model
{
    use HasFactory, ImageStorageTrait;

    protected $imageSetting = [
        'folder' => 'activities',
        'field'  => [
            'image',
        ],
    ];

    public function scopeSaved($query)
    {
        $query->where('position', '!=', null);
    }
}
