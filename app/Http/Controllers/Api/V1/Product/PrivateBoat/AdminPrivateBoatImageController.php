<?php

namespace App\Http\Controllers\Api\V1\Product\PrivateBoat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\PrivateBoat\SavePrivateBoatImageRequest;
use App\Http\Requests\Api\Product\PrivateBoat\StorePrivateBoatImageRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Http\Resources\Product\PrivateBoat\PrivateBoatImageResource;
use App\Models\PrivateBoat;
use App\Models\PrivateBoatImage;
use App\Services\Admin\AdminPermissionService;
use App\Services\Images\ImageService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class AdminPrivateBoatImageController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    public function __construct()
    {
        $this->permissionService = new AdminPermissionService('private_boat_image');
    }

    public function index(PrivateBoat $privateBoat)
    {
        $images = $privateBoat->images()->saved()->get();

        return PrivateBoatImageResource::collection($images);
    }

    public function store(PrivateBoat $privateBoat,StorePrivateBoatImageRequest $request)
    {
        try {
            DB::beginTransaction();

            $privateBoatImage   = new PrivateBoatImage();
            $imageService  = new ImageService($privateBoatImage->imagePath(), $request->image);
            $imageService->save();

            $privateBoatImage->private_boat_id  = $privateBoat->id;
            $privateBoatImage->image      = $imageService->getImageName();
            $privateBoatImage->position   = null;
            $privateBoatImage->save();

            DB::commit();

            return $this->successGet(
                new PrivateBoatImageResource($privateBoatImage)
            );

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    public function saveImages(SavePrivateBoatImageRequest $request,PrivateBoat $privateBoat)
    {
        try {
            DB::beginTransaction();

            $imagesId       = $request->validated('images_id');
            $savedImages    = $privateBoat->images()->whereIn('id', $imagesId)->get();
            $unsavedImages  = $privateBoat->images()->whereNotIn('id', $imagesId)->get();

            foreach ($imagesId as $position => $id) {
                $image           = $savedImages->where('id', '=', $id)->firstOrFail();
                $image->position = $position + 1;
                $image->save();
            }

            foreach ($unsavedImages as $unsavedImage) {
                if ($unsavedImage->privateBoatOrderDetails()->exists()) {
                    $unsavedImage->delete();
                } else {
                    $unsavedImage->forceDelete();
                    $imageService = new ImageService($unsavedImage->imagePath());
                    $imageService->delete($unsavedImage->image);
                }

            }

            DB::commit();

            return $this->successResponse();

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }

    }
}
