<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use App\Services\Logs\AgentLogService;
use App\Services\Logs\UserLogService;
use App\Services\Logs\Xendit\XenditPayoutLinkLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XenditPayoutLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'xendit_payout_id',
        'reference_id',
        'amount',
        'status',
        'expired_at',
    ];

    // public function orderRefund()
    // {
    //     return $this->hasOne(OrderRefund::class, 'xendit_payout_link_id', 'id');
    // }

    public static function boot()
    {
        parent::boot();

        $logService = new UserLogService;

        if (auth()->guard('api_admin')->check()) {
            $logService = new AdminLogService;
        }

        $xenditPayoutLinkLog = new XenditPayoutLinkLogService;
  
        static::created(function($data) use($logService, $xenditPayoutLinkLog) {
            $logService->store('create', 'xendit payout link', $data->id);

            $xenditPayoutLinkLog->store($data->id, $data->status);
        });

        static::updated(function($data) use($logService, $xenditPayoutLinkLog) {  
            if ($logService) $logService->store('update', 'xendit payout link', $data->id);

            $xenditPayoutLinkLog->store($data->id, $data->status);
        });
        
        static::deleted(function($data) use($logService) { 
            if ($logService) $logService->store('delete', 'xendit payout link', $data->id);
        });
    }
}
