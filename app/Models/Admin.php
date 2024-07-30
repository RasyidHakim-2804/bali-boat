<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Services\Logs\AdminLogService;
use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;

class Admin extends Authenticatable
{
    use HasApiTokens, ImageStorageTrait, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'is_active',
    ];

    protected $imageSetting = [
        'folder' => 'admin',
        'field'  => [
            'profile_picture',
        ],
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function forgetPasswordResets()
    {
        return $this->hasMany(ForgetAdminPasswordReset::class, 'admin_id', 'id');
    }

    public function permissions()
    {
        return $this->hasMany(AdminPermission::class, 'admin_id', 'id');
    }

    public function scopeSearch(Builder $query, $search): Builder
    {
        return $query->where('uuid', 'LIKE','%' . $search . '%')->orWhere('first_name', 'LIKE','%' . $search . '%')->orWhere('last_name', 'LIKE','%' . $search . '%');
    }

    public static function boot()
    {
        parent::boot();

        $logService = new AdminLogService;
  
        static::created(function($data) use($logService) {
            $logService->store('create', 'admin', $data->id);
        });

        static::updated(function($data) use($logService) {  
            if ($logService) $logService->store('update', 'admin', $data->id);
        });
        
        static::deleted(function($data) use($logService) { 
            if ($logService) $logService->store('delete', 'admin', $data->id);
        });
    }
}
