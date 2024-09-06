<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use App\Services\Logs\AgentLogService;
use App\Services\Logs\UserLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefundRequestPin extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'pin',
    ];

    public function getRouteKeyName()
    {
        return 'pin';
    }

    public $timestamps = false;

    public function fastBoatOrder()
    {
        return $this->belongsTo(fastBoatOrder::class, 'fast_order_order_id', 'id');
    }

    public function privateBoatOrder()
    {
        return $this->belongsTo(PrivateBoatOrder::class, 'private_order_order_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        $logService = new UserLogService;

        if (auth()->guard('api_admin')->check()) {
            $logService = new AdminLogService;
        }
  
        static::created(function($data) use($logService) {
            $logService->store('create', 'refund request pin', $data->id);
        });

        static::updated(function($data) use($logService) {  
            if ($logService) $logService->store('update', 'refund request pin', $data->id);
        });
        
        static::deleted(function($data) use($logService) { 
            if ($logService) $logService->store('delete', 'refund request pin', $data->id);
        });
    }
}
