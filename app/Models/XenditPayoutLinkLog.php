<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XenditPayoutLinkLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'xendit_payout_link_id',
        'status',
        'created_at',
    ];

    public $timestamps = false;

    public function xenditPayoutLink()
    {
        return $this->belongsTo(XenditPayoutLink::class, 'xendit_payout_link_id', 'id');
    }
}
