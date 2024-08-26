<?php

namespace App\Services\Logs\Xendit;

use App\Models\XenditPayoutLinkLog;

class XenditPayoutLinkLogService
{
    public function store($id, $status)
    {
        $logXenditPayoutLink                        = new XenditPayoutLinkLog();
        $logXenditPayoutLink->xendit_payout_link_id = $id;
        $logXenditPayoutLink->status                = $status;

        return $logXenditPayoutLink->save();
    }
}