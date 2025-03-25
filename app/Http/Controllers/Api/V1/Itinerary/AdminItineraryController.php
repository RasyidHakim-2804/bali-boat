<?php

namespace App\Http\Controllers\Api\V1\Itinerary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Itinerary\ItineraryRequest;
use App\Http\Resources\Itinerary\AdminDetailItineraryResource;
use App\Http\Resources\Itinerary\ListItineraryResource;
use App\Models\ProductItinerary;
use App\Services\Images\ImageService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AdminItineraryController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        // $this->permissionService = (new AdminPermissionService('article'));
    }

    public function index()
    {
        $itineraries = QueryBuilder::for(ProductItinerary::class)
                        ->allowedFilters([
                            AllowedFilter::exact('type'),
                            AllowedFilter::scope('search'),
                        ])
                        ->with([
                            'translations' => fn($query) => $query->language('en')
                        ])
                        ->paginate(25);

        return ListItineraryResource::collection($itineraries);
    }

    public function all()
    {
        $itineraries = QueryBuilder::for(ProductItinerary::class)
                        ->allowedFilters([
                            AllowedFilter::exact('type')
                        ])
                        ->with([
                            'translations' => fn($query) => $query->language('en')
                        ])->get();

        return ListItineraryResource::collection($itineraries);
    }

    public function show(ProductItinerary $itinerary)
    {
        $itinerary = $itinerary->load('translations');

        return $this->successGet(
            new AdminDetailItineraryResource($itinerary)
        );
    }

    public function store(ItineraryRequest $request)
    {
        return $request->validated();
        
        DB::transaction(function() use ($request) {

            $itinerary        = new ProductItinerary();
            $itinerary->type  = $request->type;
            $itinerary->image = $this->setImage($itinerary, $request->image);
            $itinerary->save();

            foreach ($request->validated('description') as $lang => $description) {
                $this->setTranslations($itinerary, $lang, 'description', $description);
            }

            foreach ($request->validated('title') as $lang => $title) {
                $this->setTranslations($itinerary, $lang, 'title', $title);
            }
        });

        return $this->successStore();
    }

    public function update(ProductItinerary $itinerary, ItineraryRequest $request)
    {
        DB::transaction(function() use ($request, $itinerary) {

            if ($request->image != null) {
                $itinerary->image = $this->setImage($itinerary, $request->image);
            }
            $itinerary->type      = $request->type;
            $itinerary->save();

            foreach ($request->validated('description') as $lang => $description) {
                $this->setTranslations($itinerary, $lang, 'description', $description);
            }

            foreach ($request->validated('title') as $lang => $title) {
                $this->setTranslations($itinerary, $lang, 'title', $title);
            }
        });

        return $this->successResponse();
    }

    public function destroy(ProductItinerary $itinerary)
    {
        DB::beginTransaction();

        try {

            $itinerary->activities()->detach();
            $itinerary->delete();

            DB::commit();

            return $this->successResponse();

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // Private function

    protected function setTranslations(ProductItinerary $itinerary, $lang, $field, $translation)
    {
        $itineraryTranslation = $itinerary->translations()->where('language',$lang)->where('field', $field)->firstOrNew();

        $itineraryTranslation->itinerary_id   = $itinerary->id;
        $itineraryTranslation->language     = $lang;
        $itineraryTranslation->field        = $field;
        $itineraryTranslation->translation  = $translation;
        $itineraryTranslation->save();
    }

    protected function setImage(ProductItinerary $itinerary, $requestImage)
    {
        $image = new ImageService($itinerary->imagePath());

        if ($itinerary->image) {
            $image->delete($itinerary->image);
        }

        $image->save($requestImage);

        return $image->getImageName();
    }
}
