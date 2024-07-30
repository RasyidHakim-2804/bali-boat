<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForgetAdminPasswordReset extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'pin',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id', 'email');
    }

    public static function boot() {
  
        parent::boot();

        $logService = new AdminLogService;
  
        static::created(function($data) use($logService) {   
            $logService->store('create', 'forget password pin', $data->id);
        });

        static::deleted(function($data) use($logService) {   
            $logService->store('delete', 'forget password pin', $data->id);
        });
    }
}
