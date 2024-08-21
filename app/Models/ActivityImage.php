<?php

namespace App\Models;

use App\Observers\AdminActionObserver;
use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([AdminActionObserver::class])]
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
