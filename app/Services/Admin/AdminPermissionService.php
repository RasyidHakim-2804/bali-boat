<?php

namespace App\Services\Admin;

use App\Models\AgentCommissionHistory;

class AdminPermissionService
{
    protected $feature;

    public function __construct($feature) {
        $this->feature = $feature;
    }

    public function check($type)
    {
        $admin   = request()->user();
        $feature = $admin->permissions()->whereHas('feature', function($feature) {
            $feature->where('name', $this->feature);
        })->first();

        if (($admin->id != 1) && (!$feature)) return abort(403, "You don't have the permission.");

        if (($admin->id != 1) && ($type == 'full') && ($feature->type != 'full')) return abort(403, "You don't have the permission.");
    }
}