<?php

namespace App\Services\Logs;

use App\Models\AdminLog;

class AdminLogService
{
    public function store($action, $table, $id, $primaryKey = 'id')
    {
        $admin = request()->user();

        $logAdmin              = new AdminLog();
        $logAdmin->admin_id    = $admin->id ?? null;
        $logAdmin->description = $action . ' ' . $table . ' with ' . $primaryKey . ' ' . $id;

        return $logAdmin->save();
    }
}