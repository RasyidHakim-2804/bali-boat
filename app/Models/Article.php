<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Article extends Model
{
    use HasFactory, ImageStorageTrait, HasSlug;

    protected $fillable = [
        'title',
        'type',
        'tag'
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id', 'id');
    }

    public function bodyTranslations()
    {
        return $this->hasMany(ArticleTranslation::class, 'article_id', 'id')->where('field', 'body');
    }

    public function metaTranslations()
    {
        return $this->hasMany(ArticleTranslation::class, 'article_id', 'id')->where('field', 'meta');
    }

    protected $imageSetting = [
        'folder' => 'articles',
        'field'  => [
            'image',
        ],
    ];

    public static function boot()
    {
        parent::boot();

        $logService = new AdminLogService;
  
        static::created(function($data) use($logService) {
            $logService->store('create', 'article', $data->id);
        });

        static::updated(function($data) use($logService) {  
            if ($logService) $logService->store('update', 'article', $data->id);
        });
        
        static::deleted(function($data) use($logService) { 
            if ($logService) $logService->store('delete', 'article', $data->id);
        });
    }
}
