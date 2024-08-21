<?php

namespace App\Http\Controllers\Api\V1\Product\PrivateBoat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\PrivateBoat\PrivateBoatRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Product\PrivateBoat\AdminPrivateBoatResource;
use App\Models\PrivateBoat;
use App\Services\Admin\AdminPermissionService;
use Illuminate\Support\Facades\DB;

class AdminPrivateBoatController extends Controller
{
    protected $permissionService;

    public function __construct()
    {
        $this->permissionService = new AdminPermissionService('privateBoat');
    }
    
    public function index()
    {
        $privateBoats = PrivateBoat::with([
            'defaultVariant'
        ])->paginate(25);

        return AdminPrivateBoatResource::collection($privateBoats);
    }

    public function show(PrivateBoat $privateBoat)
    {
        $privateBoat = $privateBoat->load([
            'tags', 
            'highlightTranslations',
            'metaTranslations',
            'pickupHarbor',
            'includes', 
            'specifications', 
            'defaultVariant'
        ]);

        return new AdminPrivateBoatResource($privateBoat);
    }

    public function store(PrivateBoatRequest $request)
    {
        try {
            DB::beginTransaction();

            $privateBoat                   = new PrivateBoat();
            $privateBoat->name             = $request->name;
            $privateBoat->active           = $request->active;
            $privateBoat->is_rented        = $request->is_rented;
            $privateBoat->pickup_harbor_id = $request->pickup_harbor_id;
            $privateBoat->save();

            $privateBoat->tags()->attach($request->tags);

            $this->setInclude($request->includes, $privateBoat);
            $this->setPrice($privateBoat, $request);
            $this->setSpecification($privateBoat, $request->specification);
            
            foreach ($request->highlight as $lang => $highlight) {
                $this->setTranslations($privateBoat, $lang, 'highlight', $highlight);
            }

            foreach ($request->meta as $lang => $meta) {
                $this->setTranslations($privateBoat, $lang, 'meta', $meta);
            }

            DB::commit();

            return new SuccessResource(201);

        } catch (\Throwable $th) {
            
            DB::rollBack();

            throw $th;
        }
    }

    public function update(PrivateBoat $privateBoat, PrivateBoatRequest $request)
    {
        try {
            DB::beginTransaction();

            $privateBoat->name             = $request->name;
            $privateBoat->active           = $request->active;
            $privateBoat->is_rented        = $request->is_rented;
            $privateBoat->pickup_harbor_id = $request->pickup_harbor_id;
            $privateBoat->save();

            $privateBoat->tags()->sync($request->tags);

            $this->setInclude($request->includes, $privateBoat);
            $this->setPrice($privateBoat, $request);
            $this->setSpecification($privateBoat, $request->specification);
            
            foreach ($request->highlight as $lang => $highlight) {
                $this->setTranslations($privateBoat, $lang, 'highlight', $highlight);
            }

            foreach ($request->meta as $lang => $meta) {
                $this->setTranslations($privateBoat, $lang, 'meta', $meta);
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

    protected function setInclude($includesRequest, PrivateBoat $privateBoat)
    {
        $position = 1;
        foreach ($includesRequest as $array) {
            foreach ($array as $lang => $value) {
                $include = $privateBoat->includes()->language($lang)->where('position', '=', $position)->firstOrNew();
                $include->private_boat_id   = $privateBoat->id;
                $include->position    = $position;
                $include->value       = $value;
                $include->language    = $lang;
                $include->save();   
            }
            $position++;
        }
        $privateBoat->includes()->where('position', '>', $position)->delete();
    }

    protected function setSpecification(PrivateBoat $privateBoat, $specificationRequest)
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

    protected function setTranslations(PrivateBoat $privateBoat, $lang, $field, $translation)
    {
        $rentalTranslation = $privateBoat->translations()->language($lang)->field($field)->firstOrNew();

        $rentalTranslation->private_boat_id    = $privateBoat->id;
        $rentalTranslation->language     = $lang;
        $rentalTranslation->field        = $field;
        $rentalTranslation->translation  = $translation;
        $rentalTranslation->save();
    }

    protected function setPrice(PrivateBoat $privateBoat, $request)
    {
        $price              = $privateBoat->defaultVariant()->firstOrNew();
        $price->price       = $request->price;
        $price->private_boat_id   = $privateBoat->id;
        $price->is_default  = 1;
        $price->save();
    }
}
