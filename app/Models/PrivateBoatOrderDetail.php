<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use App\Services\Logs\UserLogService;
use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateBoatOrderDetail extends Model
{
    use HasFactory, ImageStorageTrait;

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'quantity',
        'name',
        'description',
        'price',
        'total_price',
        'image',
    ];

    protected $imageSetting = [
        'folder' => 'product',
        'field'  => [
            'image',
        ],
    ];

    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(PrivateBoatOrder::class, 'order_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(PrivateBoat::class, 'product_id', 'id')->withTrashed();
    }

    public function variant()
    {
        return $this->belongsTo(PrivateBoatVariant::class, 'variant_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        $logService = new UserLogService;

        if (auth()->guard('api_admin')->check()) {
            $logService = new AdminLogService;
        }
  
        static::created(function($data) use($logService) {
            $logService->store('create', 'order detail', $data->id);
        });

        static::updated(function($data) use($logService) {  
            if ($logService) $logService->store('update', 'private order detail', $data->id);
        });
        
        static::deleted(function($data) use($logService) { 
            if ($logService) $logService->store('delete', 'private order detail', $data->id);
        });
    }
}
