<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use App\Services\Logs\UserLogService;
use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;

class PrivateBoatOrder extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function details()
    {
        return $this->hasMany(PrivateBoatOrderDetail::class, 'order_id', 'id');
    }

    // public function review()
    // {
    //     return $this->hasOne(OrderReview::class, 'order_id', 'id');
    // }

    public function payment()
    {
       return $this->hasOne(OrderPayment::class, 'private_boat_order_id', 'id');
    }

    // public function payout()
    // {
    //     return $this->hasOne(XenditPayout::class, 'order_id', 'id');
    // }

    public function refunds()
    {
        return $this->hasMany(OrderRefund::class, 'private_boat_order_id', 'id');
    }

    // public function decline()
    // {
    //     return $this->hasOne(OrderDecline::class, 'order_id', 'id');
    // }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function forms()
    {
        return $this->hasMany(privateBoatOrderForm::class, 'order_id', 'id');
    }

    // public function paymentHistories()
    // {
    //     return $this->belongsTo(Product::class, 'product_id', 'id');
    // }

    public function scopeFromDate(Builder $query, $date): Builder
    {
        return $query->where('created_at', '>=', $date);
    }

    public function scopeToDate(Builder $query, $date): Builder
    {
        return $query->where('created_at', '<=', $date);
    }

    public function scopeTransactionType(Builder $query, $type): Builder
    {
        if ($type == 'in_system') return $query->whereHas('payment');

        if ($type == 'not_in_system') return $query->doesntHave('payment');

        return $query;
    }

    public function scopeSearch(Builder $query, $search): Builder
    {
        return $query->where('uuid', 'LIKE','%' . $search . '%')->orWhereHas('user', function($user) use($search) {
            $user->where('first_name', 'LIKE','%' . $search . '%')->where('last_name', 'LIKE','%' . $search . '%');
        })->orWhereHas('details', function($details) use($search) {
            $details->where('name', 'LIKE','%' . $search . '%');
        });
    }

    public static function boot()
    {
        parent::boot();

        $logService = new UserLogService;

        if (auth()->guard('api_admin')->check()) {
            $logService = new AdminLogService;
        }
  
        static::created(function($data) use($logService) {
            $logService->store('create', 'private order', $data->id);
        });

        static::updated(function($data) use($logService) {  
            if ($logService) $logService->store('update', 'private order', $data->id);
        });
        
        static::deleted(function($data) use($logService) { 
            if ($logService) $logService->store('delete', 'private order', $data->id);
        });
    }
}
