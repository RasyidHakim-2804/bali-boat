<?php

namespace App\Http\Controllers\Api\V1\Harbor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Harbor\HarborRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Habor\HarborResource;
use App\Models\Harbor;

class AdminHarborController extends Controller
{
    public function index()
    {
        $harbor = Harbor::paginate(25);

        return HarborResource::collection($harbor);
    }

    public function all()
    {
        return HarborResource::collection(Harbor::all(['id', 'name']));
    }

    public function show(Harbor $harbor)
    {
        return new HarborResource($harbor);
    }

    public function store(HarborRequest $request)
    {
        $harbor         = new Harbor();
        $harbor->name   = $request->name;
        
        $harbor->save();

        return new SuccessResource(201);
    }

    public function update(HarborRequest $request, Harbor $harbor)
    {
        $harbor->name = $request->name;

        $harbor->save();

        return new SuccessResource();
    }

    public function destroy(Harbor $harbor)
    {
        $harbor->delete();

        return new SuccessResource();
    }
}
