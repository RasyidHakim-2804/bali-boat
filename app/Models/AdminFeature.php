<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public static function boot()
    {
        parent::boot();

        $logService = new AdminLogService;
  
        static::created(function($data) use($logService) {
            $logService->store('create', 'admin features', $data->id);
        });

        static::updated(function($data) use($logService) {  
            $logService->store('update', 'admin features', $data->id);
        });
        
        static::deleted(function($data) use($logService) { 
            $logService->store('delete', 'admin features', $data->id);
        });
    }
}
