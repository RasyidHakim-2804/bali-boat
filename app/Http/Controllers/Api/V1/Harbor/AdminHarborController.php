<?php

namespace App\Http\Controllers\Api\V1\Harbor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Harbor\HarborRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Habor\HarborResource;
use App\Models\Harbor;
use App\Services\Admin\AdminPermissionService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AdminHarborController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        // $this->permissionService = (new AdminPermissionService('article'));
    }

    public function index()
    {
        return HarborResource::collection(
            QueryBuilder::for(Harbor::class)
            ->allowedFilters([
                AllowedFilter::scope('search')
            ])
            ->paginate(25)
        );
    }

    public function all()
    {
        return HarborResource::collection(Harbor::all(['id', 'name']));
    }

    public function show(Harbor $harbor)
    {
        return $this->successGet(
            new HarborResource($harbor)
        );
    }

    public function store(HarborRequest $request)
    {
        $harbor         = new Harbor();
        $harbor->name   = $request->name;

        $harbor->save();

        return $this->successStore();
    }

    public function update(HarborRequest $request, Harbor $harbor)
    {
        $harbor->name = $request->name;

        $harbor->save();

        return $this->successResponse();
    }

    public function destroy(Harbor $harbor)
    {
        try {
            DB::beginTransaction();

            if ( $harbor->departureSchedules()->exists()||$harbor->arrivalSchedules()->exists()) {

                $message = [
                    'en'    => 'Harbors cannot be deleted because they are used in scheduled departures or arrivals.',
                    'id'    => 'Harbor tidak dapat dihapus karena digunakan pada jadwal keberangkatan atau kedatangan.',
                ];

                return $this->errorMessage(
                    $message[app()->getLocale()]
                );

            } else{

                $harbor->delete();
                DB::commit();
                return new SuccessResource();
            }

        } catch (\Throwable $th) {

            throw $th;
        }

    }
}
