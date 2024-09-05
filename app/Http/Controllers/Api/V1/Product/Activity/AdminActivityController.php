<?php

namespace App\Http\Controllers\Api\V1\Product\Activity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\Activity\ActivityRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Product\Activity\AdminActivityDetailResource;
use App\Http\Resources\Product\Activity\AdminActivityListResource;
use App\Models\Activity;
use App\Services\Admin\AdminPermissionService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class AdminActivityController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        $this->permissionService = new AdminPermissionService('activity');
    }
    
    public function index()
    {
        $activities = Activity::with([
            'thumbnailVariant'
        ])->paginate(25);

        return AdminActivityListResource::collection($activities);
    }

    public function show(Activity $activity)
    {
        $activity = $activity->load([
            'location',
            'translations',
            'tags.translations'         => fn($query) => $query->language(),
            'includes.translations'     => fn($query) => $query->language(),
            'excludes.translations'     => fn($query) => $query->language(),
            'itineraries.translations'  => fn($query) => $query->field('title')->language(),
            'defaultVariant',
            'variants'
        ]);
        
        return $this->successGet(
            new AdminActivityDetailResource($activity)
        );
    }

    public function store(ActivityRequest $request)
    {
        try {
            DB::beginTransaction();

            $activity               = new Activity();
            $activity->name         = $request->name;
            $activity->is_active    = $request->is_active;
            $activity->max_person   = $request->max_person;
            $activity->location_id  = $request->location_id;
            $activity->save();

            $activity->tags()->attach($request->tags);
            $activity->includes()->attach($this->generatePosition($request->includes));
            $activity->excludes()->attach($this->generatePosition($request->excludes));
            $activity->itineraries()->attach($this->generatePosition($request->itineraries));

            $this->setPrice($activity, $request);
            
            foreach ($request->highlight as $lang => $highlight) {
                $this->setTranslations($activity, $lang, 'highlight', $highlight);
            }

            foreach ($request->description as $lang => $description) {
                $this->setTranslations($activity, $lang, 'description', $description);
            }

            DB::commit();

            return $this->successStore();

        } catch (\Exception $e) {

            DB::rollBack();
            
            return $this->errorServer($e->getMessage());
        }
    }

    public function update(Activity $activity, ActivityRequest $request)
    {
        try {
            DB::beginTransaction();

            $activity->name         = $request->name;
            $activity->is_active    = $request->is_active;
            $activity->max_person   = $request->max_person;
            $activity->location_id  = $request->location_id;
            $activity->save();

            $activity->tags()->sync($request->tags);
            $activity->includes()->sync($this->generatePosition($request->includes));
            $activity->excludes()->sync($this->generatePosition($request->excludes));
            $activity->itineraries()->sync($this->generatePosition($request->itineraries));

            $this->setPrice($activity, $request);
            
            foreach ($request->highlight as $lang => $highlight) {
                $this->setTranslations($activity, $lang, 'highlight', $highlight);
            }

            foreach ($request->description as $lang => $description) {
                $this->setTranslations($activity, $lang, 'description', $description);
            }

            DB::commit();

            return $this->successResponse();

        } catch (\Exception $e) {

            DB::rollBack();
            
            return $this->errorServer($e->getMessage());
        }
    }

    public function destroy(Activity $activity)
    {
        try {
            DB::beginTransaction();
            
            $activity->tags()->detach();
            $activity->includes()->detach();
            $activity->excludes()->detach();
            $activity->itineraries()->detach();
            $activity->translations()->delete();
            $activity->images()->delete();
            $activity->variants()->delete();
            $activity->defaultVariant()->delete();
            $activity->delete();

            DB::commit();

            return $this->successResponse();

        } catch (\Exception $e) {

            DB::rollBack();
            
            return $this->errorServer($e->getMessage());
        }
    }

    ////////////////////////
    // protected function //
    ////////////////////////

    private function generatePosition($attributes)
    {
        $position = 1;

        foreach ($attributes as $attribute_id) {
            $result[$attribute_id] = [ 'position' => $position];
            $position++;
        }

        return $result;
    }

    protected function setTranslations(Activity $activity, $lang, $field, $translation)
    {
        $rentalTranslation = $activity->translations()->where('language',$lang)->where('field', $field)->firstOrNew();

        $rentalTranslation->activity_id  = $activity->id;
        $rentalTranslation->language     = $lang;
        $rentalTranslation->field        = $field;
        $rentalTranslation->translation  = $translation;
        $rentalTranslation->save();
    }

    protected function setPrice(Activity $activity, $request)
    {
        $price              = $activity->variants()->where('is_default', '=', true)->firstOrNew();
        $price->price       = $request->price;
        $price->activity_id = $activity->id;
        $price->is_default  = 1;
        $price->save();
    }
}
