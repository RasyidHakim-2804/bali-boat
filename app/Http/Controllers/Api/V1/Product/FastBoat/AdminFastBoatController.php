<?php

namespace App\Http\Controllers\Api\V1\Product\FastBoat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\FastBoat\FastBoatRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Product\FastBoat\AdminFastBoatResource;
use App\Models\FastBoat;
use App\Services\Admin\AdminPermissionService;
use App\Services\Images\ImageService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AdminFastBoatController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        $this->permissionService = new AdminPermissionService('boat');
    }

    public function index()
    {
        $fastBoat = QueryBuilder::for(FastBoat::class)
            ->allowedFilters([
                AllowedFilter::scope('search')
            ])
            ->with([
                    'schedules' => fn($query) => $query->limit(3),
                    'schedules' => [
                        'departureHarbor',
                        'arrivalHarbor'
                ]
            ])->withCount('schedules')->paginate(25);

        return AdminFastBoatResource::collection($fastBoat);
    }
    
    public function show(FastBoat $fastBoat)
    {
        // $this->permissionService->check('view');

        return $this->successGet(
            new AdminFastBoatResource($fastBoat)
        );
    }

    public function store(FastBoatRequest $request)
    {
        try {
            DB::beginTransaction();

            $fastBoat            = new FastBoat();
            $fastBoat->image     = $this->setImage($fastBoat, $request->image);
            $fastBoat->name      = $request->name;
            $fastBoat->is_active = $request->is_active;
            $fastBoat->save();

            DB::commit();
            
            return $this->successStore();

        } catch (\Throwable $th) {
            
            DB::rollBack();

            throw $th;
        }
    }

    public function update(FastBoatRequest $request, FastBoat $fastBoat)
    {
        try {
            DB::beginTransaction();

            $fastBoat->name      = $request->name;
            $fastBoat->is_active = $request->is_active;
            
            if ($request->image != null) {
                $fastBoat->image = $this->setImage($fastBoat, $request->image);
            }
            
            $fastBoat->save();

            DB::commit();
            
            return $this->successResponse();

        } catch (\Throwable $th) {
            
            DB::rollBack();

            throw $th;
        }
    }

    public function destroy(FastBoat $fastBoat)
    {
        try {
            DB::beginTransaction();

            $fastBoat = $fastBoat->load([
                'schedules'
            ]);
    
            // soft delete schedule_variants
            
            foreach ($fastBoat->schedules as $schedule) {
                $schedule->variants()->delete();
            }

            $imageService = new ImageService($fastBoat->imagePath());
            $imageService->delete($fastBoat->image);
    
            $fastBoat->schedules()->delete();
            $fastBoat->delete();

            DB::commit();

            return $this->successResponse();

        } catch (\Exception $e) {
            
            DB::rollBack();

            return $this->errorServer($e->getMessage());
            
        }
    }

    protected function setImage(FastBoat $fastBoat, $requestImage)
    {
        $image = new ImageService($fastBoat->imagePath());
        
        if ($fastBoat->image) {
            $image->delete($fastBoat->image);
        }

        $image->save($requestImage);

        return $image->getImageName();
    }
}
