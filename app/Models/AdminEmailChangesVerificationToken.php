<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminEmailChangesVerificationToken extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_id',
        'email',
        'token',
    ];

    public function getRouteKeyName()
    {
        return 'token';
    }

    public $timestamps = false;

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        $logService = new AdminLogService;
  
        static::created(function($data) use($logService) {
            $logService->store('create', 'admin email changes verification', $data->id);
        });

        static::updated(function($data) use($logService) {  
            if ($logService) $logService->store('update', 'admin email changes verification', $data->id);
        });
        
        static::deleted(function($data) use($logService) { 
            if ($logService) $logService->store('delete', 'admin email changes verification', $data->id);
        });
    }
}
