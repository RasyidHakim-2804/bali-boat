<?php

namespace App\Http\Controllers\Api\V1\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Profile\Admin\AdminUpdateRequest;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Http\Resources\Api\V1\Profile\AdminProfileResource;
use App\Models\Admin;
use App\Service\Services\ImageServices\ImageService;
use Illuminate\Support\Facades\DB;

class AdminProfileController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show()
    {
        $admin = auth()->user();
    
        return new AdminProfileResource($admin);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminUpdateRequest $request)
    {
        try {
            DB::beginTransaction();
        
            /** @var Admin */
            $admin              = $request->user();

            $admin->first_name  = $request->first_name;
            $admin->last_name   = $request->last_name;

            if ($request->has('image')) {
                $imageService             = new ImageService($admin);
                $admin->profile_image_url = $imageService->update($request->image)->path;
            }
            
            $admin->save();

            DB::commit();

            return new SuccessResource();

        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($imageService)) {
                $imageService->rollback();
            }

            throw $e;
        }
    }
}
