<?php

namespace App\Http\Controllers\Api\V1\Location;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Location\LocationRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Location\LocationResource;
use App\Models\Location;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AdminLocationController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        // $this->permissionService = (new AdminPermissionService('article'));
    }

    public function index()
    {
        return LocationResource::collection(
            QueryBuilder::for(Location::class)
            ->allowedFilters([
                AllowedFilter::scope('search')
            ])
            ->paginate(25)
        );
    }

    public function all()
    {
        return LocationResource::collection(Location::all(['id', 'name']));
    }

    public function show(Location $location)
    {
        return $this->successGet(
            new LocationResource($location)
        );
    }

    public function store(LocationRequest $request)
    {
        $location       = new Location();
        $location->name = $request->name;
        $location->save();

        return $this->successStore();
    }

    public function update(LocationRequest $request, Location $location)
    {
        $location->name = $request->name;
        $location->save();

        return $this->successResponse();
    }

    public function destroy(Location $location)
    {
        try {
            DB::beginTransaction();

            if ( $location->activities()->exists()) {

                $message = [
                    'en'    => 'Location cannot be deleted because they are used in activities.',
                    'id'    => 'Lokasi tidak dapat dihapus karena digunakan pada jadwal aktivitas.',
                ];

                return $this->errorMessage(
                    $message[app()->getLocale()]
                );

            } else{

                $location->delete();
                DB::commit();
                return new SuccessResource();
            }

        } catch (\Throwable $th) {

            throw $th;
        }
    }
}
