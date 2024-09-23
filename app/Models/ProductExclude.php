<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class ProductExclude extends Model
{
    use HasFactory;

    public const TYPE = [
        'activity', 
        // 'private-boat'
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(ExcludeTranslation::class, 'exclude_id', 'id');
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'pivot_activity_excludes', 'exclude_id', 'activity_id');
    }

    public function scopeSearch($query, $search)
    {
        $query->addSelect('*',DB::raw(
            "( select translation from exclude_translations " .
            "where exclude_id = product_excludes.id " .
            "and field = 'description' " .
            "and language = 'en' " .
            "order by id asc limit 1) as description"
        ))->having('description', 'LIKE','%' . $search . '%');
    }
}
