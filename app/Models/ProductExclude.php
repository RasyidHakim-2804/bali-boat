<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductExclude extends Model
{
    use HasFactory;

    public function translations(): HasMany
    {
        return $this->hasMany(ExcludeTranslation::class, 'exclude_id', 'id');
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'pivot_activity_excludes', 'exclude_id', 'activity_id');
    }
}
