<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OrderPaymentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_provider',
        'status'
    ];

    public $timestamps = false;

    public function scopeSearch(Builder $query, $search): Builder
    {
        return $query->where('description', 'LIKE','%' . $search . '%');
    }

    public function fastBoatOrder()
    {
        return $this->belongsTo(FastBoatOrder::class, 'id', 'fast_boat_order_id');
    }
}
