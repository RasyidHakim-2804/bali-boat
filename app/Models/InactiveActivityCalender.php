<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InactiveActivityCalender extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'date',
    ];

    public function products()
    {
        return $this->belongsTo(Activity::class, 'product_id', 'id')->withTrashed();
    }

    public static function boot()
    {
        parent::boot();

        $logService = new AdminLogService;
  
        static::created(function($data) use($logService) {
            $logService->store('create', 'inactive activity calender', $data->id);
        });

        static::updated(function($data) use($logService) {  
            if ($logService) $logService->store('update', 'inactive activity calender', $data->id);
        });
        
        static::deleted(function($data) use($logService) { 
            if ($logService) $logService->store('delete', 'inactive activity calender', $data->id);
        });
    }
}
