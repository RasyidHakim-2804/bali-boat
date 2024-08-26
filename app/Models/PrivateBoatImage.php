<?php

namespace App\Models;

use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateBoatImage extends Model
{
    use HasFactory, ImageStorageTrait;

    protected $imageSetting = [
        'folder' => 'private_boats',
        'field'  => [
            'image',
        ],
    ];

    public function scopeSaved(Builder $query)
    {
        $query->where('position', '!=', null);
    }
}
