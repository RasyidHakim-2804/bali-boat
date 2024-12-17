<?php

namespace App\Http\Controllers\Api\V1\Product\PrivateBoat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\PrivateBoat\PrivateBoatRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Product\PrivateBoat\AdminPrivateBoatDetailResource;
use App\Http\Resources\Product\PrivateBoat\AdminPrivateBoatListResource;
use App\Models\PrivateBoat;
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

            $privateBoat->includes()->attach($this->generatePosition($request->includes));
            $privateBoat->itineraries()->attach($this->generatePosition($request->itineraries));
            $privateBoat->tags()->attach($request->tags);

            $this->setDefaultVariant($privateBoat, $request);
            $this->setSpecification($privateBoat, $request->specification);

            foreach ($request->highlight as $lang => $highlight) {
                $this->setTranslations($privateBoat, $lang, 'highlight', $highlight);
            }

            foreach ($request->meta as $lang => $meta) {
                $this->setTranslations($privateBoat, $lang, 'meta', $meta);
            }

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

            $privateBoat->includes()->sync($this->generatePosition($request->includes));
            $privateBoat->itineraries()->sync($this->generatePosition($request->itineraries));
            $privateBoat->tags()->sync($request->tags);

            $this->setDefaultVariant($privateBoat, $request);
            $this->setSpecification($privateBoat, $request->specification);

            foreach ($request->highlight as $lang => $highlight) {
                $this->setTranslations($privateBoat, $lang, 'highlight', $highlight);
            }

            foreach ($request->meta as $lang => $meta) {
                $this->setTranslations($privateBoat, $lang, 'meta', $meta);
            }

            DB::commit();

            return $this->successResponse();

        } catch (\Exception $e) {

            DB::rollBack();

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

    private function setTranslations(PrivateBoat $privateBoat, $lang, $field, $translation)
    {
        $rentalTranslation = $privateBoat->translations()->language($lang)->field($field)->firstOrNew();

        $rentalTranslation->private_boat_id = $privateBoat->id;
        $rentalTranslation->language        = $lang;
        $rentalTranslation->field           = $field;
        $rentalTranslation->translation     = $translation;
        $rentalTranslation->save();
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
