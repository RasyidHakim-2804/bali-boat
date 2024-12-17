<?php

namespace App\Http\Controllers\Api\V1\Tag;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tag\TagRequest;
use App\Http\Resources\Tag\AdminDetailTagResource;
use App\Http\Resources\Tag\ListTagResource;
use App\Models\Tag;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

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
        $tags = QueryBuilder::for(Tag::class)
                    ->allowedFilters([
                        AllowedFilter::exact('type'),
                        AllowedFilter::scope('search')
                    ])
                    ->with([
                        'translations' => fn($query) => $query->language('en')
                    ])->paginate(25);

        return ListTagResource::collection($tags);
    }

    public function all()
    {
        $tags = QueryBuilder::for(Tag::class)
                    ->allowedFilters([
                        AllowedFilter::exact('type')
                    ])
                    ->with([
                        'translations' => fn($query) => $query->language('en')
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

            $tag       = new Tag();
            $tag->type = $request->type;
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
            
            $tag->type = $request->type;
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

            return $this->successResponse();

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
