<?php

namespace App\Http\Controllers\Api\V1\Product\Activity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\Activity\ActivityRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Product\Activity\AdminActivityResource;
use App\Models\Activity;
use App\Services\Admin\AdminPermissionService;
use Illuminate\Support\Facades\DB;

class AdminActivityController extends Controller
{
    protected $permissionService;

    public function __construct()
    {
        $this->permissionService = new AdminPermissionService('activity');
    }
    
    public function index()
    {
        $activities = Activity::with([
            'defaultVariant'
        ])->paginate(25);

        return AdminActivityResource::collection($activities);
    }

    public function show(Activity $activity)
    {
        $activity = $activity->load([
            'tags',
            'location', 
            'highlightTranslations',
            'descriptionTranslations',
            'includes',
            'excludes',
            'itinararies',
            'defaultVariant'
        ]);

        return new SuccessResource(
            data: new AdminActivityResource($activity)
        );
    }

    public function store(ActivityRequest $request)
    {
        try {
            DB::beginTransaction();

            $activity               = new Activity();
            $activity->name         = $request->name;
            $activity->active       = $request->active;
            $activity->max_person   = $request->max_person;
            $activity->location_id  = $request->location_id;
            $activity->save();

            $activity->tags()->attach($request->tags);

            $this->setInclude($request->includes, $activity);
            $this->setExclude($request->excludes, $activity);
            $this->setPrice($activity, $request);
            $this->setItineraries($activity, $request->itinararies);
            
            foreach ($request->highlight as $lang => $highlight) {
                $this->setTranslations($activity, $lang, 'highlight', $highlight);
            }

            foreach ($request->description as $lang => $description) {
                $this->setTranslations($activity, $lang, 'description', $description);
            }

            DB::commit();

            return new SuccessResource(201);

        } catch (\Throwable $th) {
            
            DB::rollBack();

            throw $th;
        }
    }

    public function update(Activity $activity, ActivityRequest $request)
    {
        try {
            DB::beginTransaction();

            $activity->name         = $request->name;
            $activity->active       = $request->active;
            $activity->max_person   = $request->max_person;
            $activity->location_id  = $request->location_id;
            $activity->save();

            $activity->tags()->sync($request->tags);

            $this->setInclude($request->includes, $activity);
            $this->setExclude($request->excludes, $activity);
            $this->setPrice($activity, $request);
            $this->setItineraries($activity, $request->itinararies);
            
            foreach ($request->highlight as $lang => $highlight) {
                $this->setTranslations($activity, $lang, 'highlight', $highlight);
            }

            foreach ($request->description as $lang => $description) {
                $this->setTranslations($activity, $lang, 'description', $description);
            }

            DB::commit();

            return new SuccessResource();

        } catch (\Throwable $th) {
            
            DB::rollBack();

            throw $th;
        }
    }

    public function destroy()
    {
        
    }

    ////////////////////////
    // protected function //
    ////////////////////////

    protected function setInclude($includesRequest, Activity $activity)
    {
        $position = 1;
        foreach ($includesRequest as $array) {
            foreach ($array as $lang => $value) {
                $include = $activity->includes()->language($lang)->where('position', '=', $position)->firstOrNew();
                $include->activity_id = $activity->id;
                $include->position    = $position;
                $include->value       = $value;
                $include->language    = $lang;
                $include->save();
            }
            $position++;
        }
        $activity->includes()->where('position', '>', $position)->delete();
    }

    protected function setExclude($excludesRequest, Activity $activity)
    {
        $position = 1;
        foreach ($excludesRequest as $array) {
            foreach ($array as $lang => $value) {
                $exclude = $activity->excludes()->language($lang)->where('position', '=', $position)->firstOrNew();
                $exclude->activity_id = $activity->id;
                $exclude->position    = $position;
                $exclude->value       = $value;
                $exclude->language    = $lang;
                $exclude->save();
            }
            $position++;
        }
        $activity->excludes()->where('position', '>', $position)->delete();
    }

    protected function setItineraries(Activity $activity, $itinerariesRequest)
    {
        $position = 1;
        foreach ($itinerariesRequest as $array) {
            foreach ($array as $lang => $value) {
                $itinerary = $activity->itinararies()->language($lang)->where('position', '=', $position)->firstOrNew();
                $itinerary->activity_id = $activity->id;
                $itinerary->language    = $lang;
                $itinerary->position    = $position;
                $itinerary->type        = $value['type'];
                $itinerary->title       = $value['title'];
                $itinerary->description = $value['description'];
                $itinerary->save();

            }
            $position++;
        }
        $activity->itinararies()->where('position', '>', $position)->delete();
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
        $price              = $activity->defaultVariant()->firstOrNew();
        $price->price       = $request->price;
        $price->activity_id = $activity->id;
        $price->is_default  = 1;
        $price->save();
    }
}
