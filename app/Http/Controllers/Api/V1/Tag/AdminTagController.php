<?php

namespace App\Http\Controllers\Api\V1\Tag;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tag\TagRequest;
use App\Http\Resources\Tag\AdminDetailTagResource;
use App\Http\Resources\Tag\ListTagResource;
use App\Models\Tag;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class AdminTagController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        // $this->permissionService = (new AdminPermissionService('article'));
    }

    public function index()
    {
        $tags = Tag::with([
            'translations' => fn($query) => $query->where('language', '=', app()->getLocale())
        ])->paginate(25);

        return ListTagResource::collection($tags);
    }

    public function all()
    {
        $tags = Tag::with([
            'translations' => fn($query) => $query->where('language', '=', 'id')
        ])->get();

        return ListTagResource::collection($tags);
    }

    public function show(Tag $tag)
    {
        $tag->load('translations');

        return $this->successGet(
            new AdminDetailTagResource($tag)
        );
    }

    public function store(TagRequest $request)
    {
        DB::transaction(function() use ($request) {

            $tag = new Tag();
            $tag->save();
            
            foreach ($request->validated('name') as $lang => $name) {
                $this->setTranslations($tag, $lang, 'name', $name);
            }
        });

        return $this->successStore();
    }

    public function update(Tag $tag, TagRequest $request)
    {
        DB::transaction(function() use ($request, $tag) {
            
            $tag->save();
            
            foreach ($request->validated('name') as $lang => $name) {
                $this->setTranslations($tag, $lang, 'name', $name);
            }
            
        });

        return $this->successResponse();
    }

    public function destroy(Tag $tag)
    {
        DB::beginTransaction();

        try {
        
            $tag->privateBoats()->detach();
            $tag->activities()->detach();
            $tag->delete();

            DB::commit();

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // Private function

    protected function setTranslations(Tag $tag, $lang, $field, $translation)
    {
        $tagTranslation = $tag->translations()->where('language',$lang)->where('field', $field)->firstOrNew();

        $tagTranslation->tag_id   = $tag->id;
        $tagTranslation->language     = $lang;
        $tagTranslation->field        = $field;
        $tagTranslation->translation  = $translation;
        $tagTranslation->save();
    }
}
