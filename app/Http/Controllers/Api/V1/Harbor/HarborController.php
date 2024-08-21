<?php

namespace App\Http\Controllers\Api\V1\Harbor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Habor\HarborResource;
use App\Models\Harbor;

class HarborController extends Controller
{
    public function index()
    {
        $harbor = Harbor::all();

        return HarborResource::collection($harbor);
    }
}
