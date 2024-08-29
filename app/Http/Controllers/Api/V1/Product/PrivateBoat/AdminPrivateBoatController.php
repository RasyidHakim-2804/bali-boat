<?php

namespace App\Http\Controllers\Api\V1\Product\PrivateBoat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\PrivateBoat\PrivateBoatRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Product\PrivateBoat\AdminPrivateBoatDetailResource;
use App\Http\Resources\Product\PrivateBoat\AdminPrivateBoatListResource;
use App\Models\PrivateBoat;
use App\Services\Admin\AdminPermissionService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

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
        $privateBoats = PrivateBoat::with([
            'thumbnailVariant',
        ])->paginate(25);

        return AdminPrivateBoatListResource::collection($privateBoats);
    }

    public function show(PrivateBoat $privateBoat)
    {
        $privateBoat = $privateBoat->load([
            'pickupHarbor',
            'translations',
            'tags.translations'     => fn($query) => $query->language(),
            'includes.translations' => fn($query) => $query->language(), 
            'specifications', 
            'defaultVariant',
            'variants'
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
            $privateBoat->is_rented        = $request->is_rented;
            $privateBoat->pickup_harbor_id = $request->pickup_harbor_id;
            $privateBoat->save();
            
            $privateBoat->includes()->attach($this->generatePosition($request->includes));
            $privateBoat->tags()->attach($request->tags);

            $this->setPrice($privateBoat, $request);
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
            $privateBoat->is_rented        = $request->is_rented;
            $privateBoat->pickup_harbor_id = $request->pickup_harbor_id;
            $privateBoat->save();

            $privateBoat->includes()->sync($this->generatePosition($request->includes));
            $privateBoat->tags()->sync($request->tags);

            $this->setPrice($privateBoat, $request);
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
            
            $privateBoat->tags()->detach();
            $privateBoat->includes()->detach();
            $privateBoat->specifications()->delete();
            $privateBoat->translations()->delete();
            $privateBoat->images()->delete();
            $privateBoat->variants()->delete();
            $privateBoat->defaultVariant()->delete();
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

    private function setSpecification(PrivateBoat $privateBoat, $specificationRequest)
    {
        foreach ($specificationRequest as $lang => $value) {

            $specification = $privateBoat->specifications()->language($lang)->firstOrNew();
            
            $specification->private_boat_id = $privateBoat->id;
            $specification->language  = $lang;
            $specification->model     = $value['model'];
            $specification->year      = $value['year'];
            $specification->length    = $value['length'];
            $specification->cabins    = $value['cabins'];
            $specification->heads     = $value['heads'];
            $specification->engine    = $value['engine'];
            $specification->save();
        }
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

    private function setPrice(PrivateBoat $privateBoat, $request)
    {
        $price                  = $privateBoat->defaultVariant()->firstOrNew();
        $price->price           = $request->price;
        $price->private_boat_id = $privateBoat->id;
        $price->is_default      = true;
        $price->save();
    }
}
