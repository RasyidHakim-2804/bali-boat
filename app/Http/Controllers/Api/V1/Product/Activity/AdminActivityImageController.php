<?php

namespace App\Http\Controllers\Api\V1\Product\Activity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\Activity\SaveActivityImageRequest;
use App\Http\Requests\Api\Product\Activity\StoreActivityImageRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Product\Activity\ActivityImageResource;
use App\Models\Activity;
use App\Models\ActivityImage;
use App\Services\Admin\AdminPermissionService;
use App\Services\Images\ImageService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class AdminActivityImageController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        $this->permissionService = new AdminPermissionService('activity_image');
    }

    public function index(Activity $activity)
    {
        $images = $activity->images()->saved()->get();

        return ActivityImageResource::collection($images);
    }

    public function store(Activity $activity, StoreActivityImageRequest $request)
    {
        try {
            DB::beginTransaction();

            $activityImage = new ActivityImage();
            $imageService  = new ImageService($activityImage->imagePath(), $request->image);
            $imageService->save();          

            $activityImage->activity_id  = $activity->id;
            $activityImage->image        = $imageService->getImageName();
            $activityImage->position     = null;
            $activityImage->save();

            DB::commit();

            return $this->successGet(
                new ActivityImageResource($activityImage)
            );

        } catch (\Throwable $th) {
            
            DB::rollBack();

            throw $th;
        }
    }

    public function saveImages(SaveActivityImageRequest $request, Activity $activity)
    {
        try {
            DB::beginTransaction();

            $imagesId       = $request->validated('images_id');
            $savedImages    = $activity->images()->whereIn('id', $imagesId)->get();
            $unsavedImages  = $activity->images()->whereNotIn('id', $imagesId)->get();                                                                   

            $activity->images()->whereNotIn('id', $imagesId)->delete(); 
            
            foreach ($imagesId as $position => $id) {
                $image           = $savedImages->where('id', '=', $id)->firstOrFail();
                $image->position = $position + 1;
                $image->save();
            }

            foreach ($unsavedImages as $unsavedImage) {
                $imageService = new ImageService($unsavedImage->imagePath());
                $imageService->delete($unsavedImage->image);
            }

            DB::commit();            

            return $this->successResponse();

        } catch (\Throwable $th) {
            
            DB::rollBack();
            
            throw $th;
        }
            
    }
}
