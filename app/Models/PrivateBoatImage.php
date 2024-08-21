<?php

namespace App\Models;

use App\Observers\AdminActionObserver;
use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([AdminActionObserver::class])]
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
