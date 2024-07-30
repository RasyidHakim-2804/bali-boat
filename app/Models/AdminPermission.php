<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'feature_id',
        'type',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function feature()
    {
        return $this->hasOne(AdminFeature::class, 'id', 'feature_id');
    }

    public static function boot()
    {
        parent::boot();

        $logService = new AdminLogService;
  
        static::created(function($data) use($logService) {
            $logService->store('create', 'admin permission', $data->id);
        });

        static::updated(function($data) use($logService) {  
            $logService->store('update', 'admin permission', $data->id);
        });
        
        static::deleted(function($data) use($logService) { 
            $logService->store('delete', 'admin permission', $data->id);
        });
    }
}
