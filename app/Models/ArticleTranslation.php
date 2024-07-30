<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'language',
        'filed',
        'translation',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        $logService = new AdminLogService;
  
        static::created(function($data) use($logService) {
            $logService->store('create', 'article translation', $data->id);
        });

        static::updated(function($data) use($logService) {  
            $logService->store('update', 'article translation', $data->id);
        });
        
        static::deleted(function($data) use($logService) { 
            $logService->store('delete', 'article translation', $data->id);
        });
    }
}
