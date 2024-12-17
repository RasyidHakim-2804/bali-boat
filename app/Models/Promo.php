<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promo extends Model
{
    use HasFactory, SoftDeletes, ImageStorageTrait;

    protected $fillable = [
        'code',
        'description',
        'type',
        'duration',
        'amount',
        'is_active',
        'is_all_date',
        'is_all_day',
        'start_at',
        'expired_at',
        'for_start_date',
        'for_end_date',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $imageSetting = [
        'folder' => 'promo',
        'field'  => [
            'image',
        ],
    ];

    public function days()
    {
        return $this->hasMany(PromoDay::class, 'promo_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        $logService = new AdminLogService;
  
        static::created(function($data) use($logService) {
            $logService->store('create', 'promo', $data->id);
        });

        static::updated(function($data) use($logService) {  
            if ($logService) $logService->store('update', 'promo', $data->id);
        });
        
        static::deleted(function($data) use($logService) { 
            if ($logService) $logService->store('delete', 'promo', $data->id);
        });
    }
}
