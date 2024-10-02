<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'day',
    ];

    public $timestamps = false;

    public function promo()
    {
        return $this->hasOne(Promo::class, 'promo_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        $logService = new AdminLogService;
  
        static::created(function($data) use($logService) {
            $logService->store('create', 'promo day', $data->id);
        });

        static::updated(function($data) use($logService) {  
            if ($logService) $logService->store('update', 'promo day', $data->id);
        });
        
        static::deleted(function($data) use($logService) { 
            if ($logService) $logService->store('delete', 'promo day', $data->id);
        });
    }
}
