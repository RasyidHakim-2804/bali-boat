<?php

namespace App\Http\Controllers\Api\V1\Include;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Include\IncludeRequest;
use App\Http\Resources\Include\AdminDetailIncludeResource;
use App\Http\Resources\Include\ListIncludeResource;
use App\Models\ProductInclude;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AdminIncludeController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        // $this->permissionService = (new AdminPermissionService('article'));
    }

    public function index()
    {
        $includes = QueryBuilder::for(ProductInclude::class)
                    ->allowedFilters([
                        AllowedFilter::exact('type'),
                        AllowedFilter::scope('search'),
                    ])
                    ->with([
                        'translations' => fn($query) => $query->language('en')
                    ])
                    ->paginate(25);

        return ListIncludeResource::collection($includes);
    }

    public function all()
    {
        $includes = QueryBuilder::for(ProductInclude::class)
                    ->allowedFilters([
                        AllowedFilter::exact('type')
                    ])
                    ->with([
                        'translations' => fn($query) => $query->language('en')
                    ])->get();

        return ListIncludeResource::collection($includes);
    }

    public function show(ProductInclude $include)
    {
        $include->load('translations');

        return $this->successGet(
            new AdminDetailIncludeResource($include)
        );
    }

    public function store(IncludeRequest $request)
    {
        DB::transaction(function() use ($request) {

            $include       = new ProductInclude();
            $include->type = $request->type;
            $include->save();
            
            foreach ($request->validated('description') as $lang => $description) {
                $this->setTranslations($include, $lang, 'description', $description);
            }
        });

        return $this->successStore();
    }

    public function update(ProductInclude $include, IncludeRequest $request)
    {
        DB::transaction(function() use ($request, $include) {
            
            $include->type = $request->type;
            $include->save();
            
            foreach ($request->validated('description') as $lang => $description) {
                $this->setTranslations($include, $lang, 'description', $description);
            }
        });

        return $this->successResponse();
    }

    public function destroy(ProductInclude $include)
    {
        DB::beginTransaction();

        try {
        
            $include->privateBoats()->detach();
            $include->activities()->detach();
            $include->delete();

            DB::commit();

            return $this->successResponse();

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // Private function

    protected function setTranslations(ProductInclude $include, $lang, $field, $translation)
    {
        $includeTranslation = $include->translations()->where('language',$lang)->where('field', $field)->firstOrNew();

        $includeTranslation->include_id   = $include->id;
        $includeTranslation->language     = $lang;
        $includeTranslation->field        = $field;
        $includeTranslation->translation  = $translation;
        $includeTranslation->save();
    }
}
