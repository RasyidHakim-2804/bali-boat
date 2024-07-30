<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminEmailVerificationToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'pin',
    ];

    public $timestamps = false;

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public static function boot() {
  
        parent::boot();

        $logService = new AdminLogService;
  
        static::created(function($data) use($logService) {   
            $logService->store('create', 'email verification token', $data->id);
        });

        static::deleted(function($data) use($logService) {   
            $logService->store('delete', 'email verification token', $data->id);
        });
    }
}
