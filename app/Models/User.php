<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Services\Logs\AdminLogService;
use App\Services\Logs\UserLogService;
use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasApiTokens, ImageStorageTrait, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'country',
        'city',
        'phone',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $imageSetting = [
        'folder' => 'user',
        'field'  => [
            'profile_picture',
        ],
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function emailVerifications()
    {
        return $this->hasMany(UserEmailVerificationToken::class, 'user_id', 'id');
    }

    public function forgetPasswordResets()
    {
        return $this->hasMany(ForgetUserPasswordReset::class, 'user_id', 'id');
    }

    // public function cart()
    // {
    //     return $this->hasOne(Cart::class, 'user_id', 'id');
    // }

    public function fastBoatOrders()
    {
        return $this->hasMany(FastBoatOrder::class, 'user_id', 'id');
    }

    // public function orderReviews()
    // {
    //     return $this->hasMany(OrderReview::class, 'user_id', 'id');
    // }

    public function scopeSearch(Builder $query, $search): Builder
    {
        return $query->where('uuid', 'LIKE','%' . $search . '%')->orWhere('first_name', 'LIKE','%' . $search . '%')->orWhere('last_name', 'LIKE','%' . $search . '%');
    }

    public static function boot()
    {
        parent::boot();

        $logService = new UserLogService;

        if (auth()->guard('api_admin')->check()) {
            $logService = new AdminLogService;
        }
  
        static::created(function($data) use($logService) {
            $logService->store('create', 'user', $data->id);
        });
  
        if (request()->path() != 'api/v1/user/reset-password') {
            static::updated(function($data) use($logService) {  
                if ($logService) $logService->store('update', 'user', $data->id);
            });
        }
        
        static::deleted(function($data) use($logService) { 
            if ($logService) $logService->store('delete', 'user', $data->id);
        });
    }
}
