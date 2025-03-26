<?php

namespace App\Http\Controllers\Api\V1\Product\PrivateBoat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\PrivateBoat\PrivateBoatRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Product\PrivateBoat\AdminPrivateBoatDetailResource;
use App\Http\Resources\Product\PrivateBoat\AdminPrivateBoatListResource;
use App\Models\Language;
use App\Models\PrivateBoat;
use App\Models\PrivateBoatTranslation;
use App\Services\Admin\AdminPermissionService;
use App\Services\Images\ImageService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AdminPrivateBoatController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        $this->permissionService = new AdminPermissionService('privateBoat');
    }

    public function index()
    {
        return AdminPrivateBoatListResource::collection(
            QueryBuilder::for(PrivateBoat::class)
            ->allowedFilters([
                AllowedFilter::scope('search')
            ])
            ->selectThumbnailVariantPrice()
            ->orderBy('id', 'DESC')
            ->paginate(25)
        );
    }

    public function show(PrivateBoat $privateBoat)
    {
        $privateBoat = $privateBoat->load([
            'pickupHarbor',
            'translations',
            'tags.translations'         => fn($query) => $query->language('en'),
            'includes.translations'     => fn($query) => $query->language('en'),
            'itineraries.translations'  => fn($query) => $query->language('en'),
            'specification',
            'defaultVariant',
            'thumbnailImage',
        ]);

        return $this->successGet(
            new AdminPrivateBoatDetailResource($privateBoat)
        );
    }

    public function store(PrivateBoatRequest $request)
    {
        try {
            DB::beginTransaction();

            $privateBoat                   = new PrivateBoat();
            $privateBoat->name             = $request->name;
            $privateBoat->is_active        = $request->is_active;
            $privateBoat->max_person       = $request->max_person;
            $privateBoat->rating           = $request->rating;
            $privateBoat->pickup_harbor_id = $request->pickup_harbor_id;
            $privateBoat->save();

            if ($request->includes) {
                $privateBoat->includes()->sync($this->generatePosition($request->includes));
            }

            if ($request->itineraries) {
                $privateBoat->itineraries()->sync($this->generatePosition($request->itineraries));
            }

            $privateBoat->tags()->attach($request->tags);

            $this->setDefaultVariant($privateBoat, $request);
            $this->setSpecification($privateBoat, $request->specification);
            $this->saveTranslations($privateBoat, $request);

            DB::commit();

            return $this->successStore();

        } catch (\Exception $e) {

            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function update(PrivateBoat $privateBoat, PrivateBoatRequest $request)
    {
        try {
            DB::beginTransaction();

            $privateBoat->name             = $request->name;
            $privateBoat->is_active        = $request->is_active;
            $privateBoat->max_person       = $request->max_person;
            $privateBoat->pickup_harbor_id = $request->pickup_harbor_id;
            $privateBoat->rating           = $request->rating;
            $privateBoat->save();

            if ($request->includes) {
                $privateBoat->includes()->sync($this->generatePosition($request->includes));
            }

            if ($request->itineraries) {
                $privateBoat->itineraries()->sync($this->generatePosition($request->itineraries));
            }

            $privateBoat->tags()->sync($request->tags);

            $this->setDefaultVariant($privateBoat, $request);
            $this->setSpecification($privateBoat, $request->specification);
            $this->saveTranslations($privateBoat, $request);

            DB::commit();

            return $this->successResponse();

        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;

            return $this->errorServer($e->getMessage());
        }
    }

    public function destroy(PrivateBoat $privateBoat)
    {
        try {
            DB::beginTransaction();

            $privateBoat = $privateBoat->load([
                'images',
                'variants'
            ]);

            foreach ($privateBoat->images as $image) {
                $imageService = new ImageService($image->imagePath());
                $imageService->delete($image->image);
                $image->delete();
            }

            foreach ($privateBoat->variants as $variant) {
                $variant->delete();
            }

            $privateBoat->tags()->detach();
            $privateBoat->includes()->detach();
            $privateBoat->specification()->delete();
            $privateBoat->translations()->delete();
            $privateBoat->delete();

            DB::commit();

            return $this->successResponse();

        } catch (\Exception $e) {

            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    ////////////////////////
    // private function //
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

    private function setSpecification(PrivateBoat $privateBoat, $request)
    {
        $specification = $privateBoat->specification()->firstOrNew();

        $specification->private_boat_id = $privateBoat->id;
        $specification->model     = $request['model'];
        $specification->year      = $request['year'];
        $specification->length    = $request['length'];
        $specification->cabins    = $request['cabins'];
        $specification->heads     = $request['heads'];
        $specification->engine    = $request['engine'];
        $specification->save();
    }

    private function saveTranslations(PrivateBoat $privateBoat, PrivateBoatRequest $request)
    {
        $old_translations = $privateBoat->translations;
        $new_translations = collect([]);
        $code             = Language::code();

        foreach (PrivateBoatTranslation::FIELD as $field) {

            $translations = collect($old_translations->where('field', '=', $field)->all());

            foreach ($code as $lang) {

                $value = isset($request->$field[$lang]) ? $request->$field[$lang] : '';

                $translation = $translations->where('language', '=', $lang)->first();

                if ($translation == null) $translation = new PrivateBoatTranslation();

                $translation->language          = $lang;
                $translation->field             = $field;
                $translation->translation       = $value;
                $translation->private_boat_id   = $privateBoat->id;

                $new_translations->add($translation);
            }
        }

        $privateBoat->translations()->saveMany($new_translations);
    }

    private function setDefaultVariant(PrivateBoat $privateBoat, $request)
    {
        $price                  = $privateBoat->defaultVariant()->firstOrNew();
        $price->price           = $request->price;
        $price->private_boat_id = $privateBoat->id;
        $price->is_default      = true;
        $price->save();
    }
}
