<?php

namespace App\Http\Controllers\Api\V1\Product\Activity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\Activity\ActivityRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Product\Activity\AdminActivityDetailResource;
use App\Http\Resources\Product\Activity\AdminActivityListResource;
use App\Models\Activity;
use App\Models\ActivityTranslation;
use App\Models\ActivityVariant;
use App\Models\Language;
use App\Services\Admin\AdminPermissionService;
use App\Services\Images\ImageService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

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
        return AdminActivityListResource::collection(
            QueryBuilder::for(Activity::class)
            ->allowedFilters([
                AllowedFilter::scope('search')
            ])
            ->selectThumbnailVariantPrice()
            ->orderBy('id', 'DESC')
            ->paginate(25)
        );
    }

    public function show(Activity $activity)
    {
        $activity = $activity->load([
            'location',
            'translations',
            'tags.translations'         => fn($query) => $query->language('en'),
            'includes.translations'     => fn($query) => $query->language('en'),
            'excludes.translations'     => fn($query) => $query->language('en'),
            'itineraries.translations'  => fn($query) => $query->field('title')->language('en'),
            'variants',
            'thumbnailImage',
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
            $activity->rating       = $request->rating;
            $activity->save();

            $activity->tags()->attach($request->tags);
            $activity->includes()->attach($this->generatePosition($request->includes));
            $activity->excludes()->attach($this->generatePosition($request->excludes));
            $activity->itineraries()->attach($this->generatePosition($request->itineraries));

            // $this->setDefaultVariant($activity, $request);
            $this->saveVariants($activity, $request);
            $this->saveTranslations($activity, $request);

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
            $activity->rating       = $request->rating;
            $activity->save();

            $activity->tags()->sync($request->tags);
            $activity->includes()->sync($this->generatePosition($request->includes));
            $activity->excludes()->sync($this->generatePosition($request->excludes));
            $activity->itineraries()->sync($this->generatePosition($request->itineraries));

            // $this->setDefaultVariant($activity, $request);
            $this->saveVariants($activity, $request);
            $this->saveTranslations($activity, $request);

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

            $activity = $activity->load([
                'images',
                'variants'
            ]);

            foreach ($activity->images as $image) {
                $imageService = new ImageService($image->imagePath());
                $imageService->delete($image->image);
                $image->delete();
            }

            foreach ($activity->variants as $variant) {
                $variant->delete();
            }

            $activity->tags()->detach();
            $activity->includes()->detach();
            $activity->excludes()->detach();
            $activity->itineraries()->detach();
            $activity->translations()->delete();
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

    private function saveTranslations(Activity $activity, ActivityRequest $request)
    {
        $old_translations = $activity->translations;
        $new_translations = collect([]);
        $code             = Language::code();

        foreach (ActivityTranslation::FIELD as $field) {

            $translations = collect($old_translations->where('field', '=', $field)->all());

            foreach ($code as $lang) {

                $value = isset($request->$field[$lang]) ? $request->$field[$lang] : '';

                $translation = $translations->where('language', '=', $lang)->first();

                if ($translation == null) $translation = new ActivityTranslation();

                $translation->language    = $lang;
                $translation->field       = $field;
                $translation->translation = $value;
                $translation->activity_id = $activity->id;

                $new_translations->add($translation);
            }
        }

        $activity->translations()->saveMany($new_translations);
    }

    protected function setDefaultVariant(Activity $activity, $request)
    {
        $price              = $activity->defaultVariant()->firstOrNew();
        $price->price       = $request->price;
        $price->activity_id = $activity->id;
        $price->is_default  = 1;
        $price->save();
    }

    protected function saveVariants(Activity $activity, $request)
    {
        $variants     = $activity->variants()->get();
        $new_variants = collect([]);

        foreach (ActivityVariant::PASSENGER_TYPE as $type) {
            foreach (ActivityVariant::NAME as $name) {
                $variant    = $variants->where('passenger_type', '=',$type)->where('name', '=', $name)->first();

                if ($variant == null) $variant = new ActivityVariant();

                $variant->price             = $request->variants[$type][$name];
                $variant->passenger_type    = $type;
                $variant->name              = $name;
                $variant->activity_id       = $activity->id;
                $variant->is_default        = 0;

                $new_variants->add($variant);
            }
        }

        $activity->variants()->saveMany($new_variants);
    }
}
