<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use App\Services\Logs\AgentLogService;
use App\Services\Logs\UserLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'payment_method',
        'payment_chanel',
        'paid_at',
        'status',
        'total_purchased',
        'url'
    ];

    public function fastBoatOrder()
    {
        return $this->belongsTo(FastBoatOrder::class, 'fast_boat_order_id', 'id');
    }

    public function privateBoatOrder()
    {
        return $this->belongsTo(FastBoatOrder::class, 'private_boat_order_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        $logService = new UserLogService;

        if (auth()->guard('api_admin')->check()) {
            $logService = new AdminLogService;
        }
  
        static::created(function($data) use($logService) {
            $logService->store('create', 'order payment', $data->id);
        });

        static::updated(function($data) use($logService) {  
            if ($logService) $logService->store('update', 'order payment', $data->id);
        });
        
        static::deleted(function($data) use($logService) { 
            if ($logService) $logService->store('delete', 'order payment', $data->id);
        });
    }
}
