<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AdminLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
    ];

    public $timestamps = false;

    public function scopeSearch(Builder $query, $search): Builder
    {
        return $query->where('description', 'LIKE','%' . $search . '%');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id', 'admin_id');
    }
}
