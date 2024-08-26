<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use App\Services\Logs\UserLogService;
use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'name',
        'date_of_birth',
        'gender',
        'nationality',
        'email',
        'phone',
        'pickup_address',
        'diet_restriction',
        'shoes_size',
        'certification_level',
        'diving_association_number',
        'last_dive',
        'is_pic',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        $logService = new UserLogService;

        if (auth()->guard('api_admin')->check()) {
            $logService = new AdminLogService;
        }
  
        static::created(function($data) use($logService) {
            $logService->store('create', 'order form', $data->id);
        });

        static::updated(function($data) use($logService) {  
            if ($logService) $logService->store('update', 'order form', $data->id);
        });
        
        static::deleted(function($data) use($logService) { 
            if ($logService) $logService->store('delete', 'order form', $data->id);
        });
    }
}
