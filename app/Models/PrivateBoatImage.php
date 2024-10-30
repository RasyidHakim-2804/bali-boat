<?php

namespace App\Models;

use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrivateBoatImage extends Model
{
    use HasFactory, ImageStorageTrait, SoftDeletes;

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

    public function privateBoatOrderDetails()
    {
        return $this->hasMany(PrivateBoatOrderDetail::class, 'image', 'image');
    }
}
