<?php

namespace App\Models;

use App\Services\Logs\UserLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEmailVerificationToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'token',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function boot() {
  
        parent::boot();

        $logService = new UserLogService;
  
        static::created(function($data) use($logService) {   
            $logService->store('create', 'email verification token', $data->id);
        });

        static::deleted(function($data) use($logService) {   
            $logService->store('delete', 'email verification token', $data->id);
        });
    }
}
