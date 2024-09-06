<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use App\Services\Logs\UserLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FastBoatOrderForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'schedule_id',
        'variant_id',
        'name',
        'nationality',
        'passenger_type',
        'age_type',
        'is_local',
        'is_itas_holder',
    ];

    public function order()
    {
        return $this->belongsTo(FastBoatOrder::class, 'order_id', 'id');
    }

    public function schedule()
    {
        return $this->hasOne(FastBoatSchedule::class, 'id', 'schedule_id');
    }

    public function scheduleVariant()
    {
        return $this->hasOne(FastBoatScheduleVariant::class, 'id', 'variant_id');
    }

    public static function boot()
    {
        parent::boot();

        $logService = new UserLogService;

        if (auth()->guard('api_admin')->check()) {
            $logService = new AdminLogService;
        }
  
        static::created(function($data) use($logService) {
            $logService->store('create', 'fast boat order form', $data->id);
        });

        static::updated(function($data) use($logService) {  
            if ($logService) $logService->store('update', 'fast boat order form', $data->id);
        });
        
        static::deleted(function($data) use($logService) { 
            if ($logService) $logService->store('delete', 'fast boat order form', $data->id);
        });
    }
}
