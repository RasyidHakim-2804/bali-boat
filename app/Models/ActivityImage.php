<?php

namespace App\Models;

use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityImage extends Model
{
    use HasFactory, ImageStorageTrait, SoftDeletes;

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

    public function activityOrderDetails()
    {
        return $this->hasMany(ActivityOrderDetail::class, 'image', 'image');
    }
}
