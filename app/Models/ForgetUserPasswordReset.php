<?php

namespace App\Models;

use App\Services\Logs\UserLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForgetUserPasswordReset extends Model
{
    use HasFactory;

    protected $table = 'forget_user_password_resets';

    protected $fillable = [
        'email',
        'pin',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    public static function boot() {
  
        parent::boot();

        $logService = new UserLogService;
  
        static::created(function($data) use($logService) {   
            $logService->store('create', 'forget password pin', $data->id);
        });

        static::deleted(function($data) use($logService) {   
            $logService->store('delete', 'forget password pin', $data->id);
        });
    }
}
