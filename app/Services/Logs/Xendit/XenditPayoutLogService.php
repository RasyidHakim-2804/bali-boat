<?php

namespace App\Services\Logs\Xendit;

use App\Models\XenditPayoutLog;

class XenditPayoutLogService
{
    public function store($id, $status)
    {
        $logXenditPayoutLink                        = new XenditPayoutLog();
        $logXenditPayoutLink->xendit_payout_link_id = $id;
        $logXenditPayoutLink->status                = $status;
        $logXenditPayoutLink->created_at            = now();

        return $logXenditPayoutLink->save();
    }
}