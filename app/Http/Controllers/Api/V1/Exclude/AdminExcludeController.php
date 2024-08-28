<?php

namespace App\Http\Controllers\Api\V1\Exclude;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Exclude\ExcludeRequest;
use App\Http\Resources\Exclude\AdminDetailExcludeResource;
use App\Http\Resources\Exclude\ListExcludeResource;
use App\Models\ProductExclude;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class AdminExcludeController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        // $this->permissionService = (new AdminPermissionService('article'));
    }

    public function index()
    {
        $excludes = ProductExclude::with([
            'translations' => fn($query) => $query->where('language', '=', app()->getLocale())
        ])->paginate(25);

        return ListExcludeResource::collection($excludes);
    }

    public function all()
    {
        $excludes = ProductExclude::with([
            'translations' => fn($query) => $query->where('language', '=', app()->getLocale())
        ])->get();

        return ListExcludeResource::collection($excludes);
    }

    public function show(ProductExclude $exclude)
    {
        $exclude->load('translations');
        
        return $this->successGet(
            new AdminDetailExcludeResource($exclude)
        );
    }

    public function store(ExcludeRequest $request)
    {
        DB::transaction(function() use ($request) {

            $exclude = new ProductExclude();
            $exclude->save();
            
            foreach ($request->validated('description') as $lang => $description) {
                $this->setTranslations($exclude, $lang, 'description', $description);
            }
        });

        return $this->successStore();
    }

    public function update(ProductExclude $exclude, ExcludeRequest $request)
    {
        DB::transaction(function() use ($request, $exclude) {
            
            $exclude->save();
            
            foreach ($request->validated('description') as $lang => $description) {
                $this->setTranslations($exclude, $lang, 'description', $description);
            }
        });

        return $this->successResponse();
    }

    public function destroy(ProductExclude $exclude)
    {
        DB::beginTransaction();

        try {
        
            $exclude->activities()->detach();
            $exclude->delete();

            DB::commit();

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // Private function

    protected function setTranslations(ProductExclude $exclude, $lang, $field, $translation)
    {
        $excludeTranslation = $exclude->translations()->where('language',$lang)->where('field', $field)->firstOrNew();

        $excludeTranslation->exclude_id   = $exclude->id;
        $excludeTranslation->language     = $lang;
        $excludeTranslation->field        = $field;
        $excludeTranslation->translation  = $translation;
        $excludeTranslation->save();
    }
}
