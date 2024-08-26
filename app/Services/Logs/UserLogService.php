<?php

namespace App\Services\Logs;

use App\Models\UserLog;

class UserLogService
{
    public function store($action, $table, $id, $primaryKey = 'id')
    {
        $user = request()->user();

        $logUser              = new UserLog();
        $logUser->user_id     = $user->id ?? null;
        $logUser->description = $action . ' ' . $table . ' with ' . $primaryKey . ' ' . $id;

        return $logUser->save();
    }
}