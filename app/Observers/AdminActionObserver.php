<?php

namespace App\Observers;

use App\Services\Logs\AdminLogService;
use Illuminate\Database\Eloquent\Model;

class AdminActionObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(Model $model): void
    {
        $adminLogService = new AdminLogService();

        $adminLogService->store('store', $model->getTable(), $model->id);
    }
 
    /**
     * Handle the User "updated" event.
     */
    public function updated(Model $model): void
    {
        $adminLogService = new AdminLogService();

        $adminLogService->store('update', $model->getTable(), $model->id);
    }
 
    /**
     * Handle the User "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $adminLogService = new AdminLogService();

        $adminLogService->store('delete', $model->getTable(), $model->id);
    }
}
