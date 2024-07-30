<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminFeature;
use App\Traits\JsonResponsesTrait;

class AdminFeatureController extends Controller
{
    use JsonResponsesTrait;

    public function index()
    {
        $data = AdminFeature::get();

        return $this->successGet($data);
    }
}
