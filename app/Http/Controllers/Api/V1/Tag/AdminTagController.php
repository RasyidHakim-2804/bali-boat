<?php

namespace App\Http\Controllers\Api\V1\Tag;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tag\TagRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Tag\TagResource;
use App\Models\Tag;
use App\Services\Admin\AdminPermissionService;
use Illuminate\Support\Facades\DB;

class AdminTagController extends Controller
{
    private $permissionService;

    public function __construct()
    {
        $this->permissionService = new AdminPermissionService('tag');
    }

    public function index()
    {
        return TagResource::collection(Tag::paginate(25));
    }

    public function all()
    {
        return TagResource::collection(Tag::all());
    }

    public function show(Tag $tag)
    {
        return new SuccessResource(
            data: new TagResource($tag)
        );
    }

    public function store(TagRequest $request)
    {
        $tag       = new Tag();
        $tag->name = $request->name;
        $tag->save();

        return new SuccessResource(201);
    }

    public function update(TagRequest $request, Tag $tag)
    {
        $tag->name = $request->name;
        $tag->save();

        return new SuccessResource();
    }

    public function destroy(Tag $tag)
    {
        try {
            DB::beginTransaction();
            
            $tag->rentals()->detach();
            $tag->delete();
            
            DB::commit();

            return new SuccessResource();
        
        } catch (\Throwable $th) {
            
            DB::rollBack();

            throw $th;
        }

    }
}

