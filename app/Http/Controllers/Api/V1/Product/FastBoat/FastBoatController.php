<?php

namespace App\Http\Controllers\Api\V1\Product\FastBoat;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\FastBoat\FastBoatResource;
use App\Models\FastBoat;

class FastBoatController extends Controller
{   
    public function index()
    {
        return FastBoatResource::collection(FastBoat::all('id', 'name'));
    }
}
