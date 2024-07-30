<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleImage extends Model
{
    use HasFactory, ImageStorageTrait;

    protected $fillable = [
        'article_id',
    ];

    protected $imageSetting = [
        'folder' => 'article',
        'field'  => [
            'image',
        ],
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
            $logService->store('create', 'article image', $data->id);
        });

        static::updated(function($data) use($logService) {  
            if ($logService) $logService->store('update', 'article image', $data->id);
        });
        
        static::deleted(function($data) use($logService) { 
            if ($logService) $logService->store('delete', 'article image', $data->id);
        });
    }
}
