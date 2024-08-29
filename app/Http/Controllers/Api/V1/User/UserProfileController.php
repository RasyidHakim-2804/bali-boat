<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Profile\User\UserUpdateRequest;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Http\Resources\Profile\UserProfileResource;
use App\Models\User;
use App\Services\Images\ImageService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class UserProfileController extends Controller
{

    use JsonResponsesTrait;
    /**
     * Display the specified resource.
     */
    public function index()
    {
        $user = new UserProfileResource(User::where('id', request()->user()->id)->firstOrFail());

        return $this->successGet($user);
    }

    // public function update(UserUpdateRequest $request)
    // {
    //     try {
    //         DB::beginTransaction();

    //         /** @var User */
    //         $user = auth()->user();
    //         $user->first_name = $request->first_name;
    //         $user->last_name  = $request->last_name;
            
    //         if ($request->has('image')) {
    //             $imageService = new ImageService($user);
    //             $user->profile_image_url = $imageService->update($request->image)->path;
    //         }
            
    //         $user->save();
            
    //         DB::commit();

    //         return new SuccessResource();

    //     } catch (\Throwable $th) {
            
    //         DB::rollBack();

    //         if (isset($imageService)) {
    //             $imageService->rollback();
    //         }

    //         throw $th;
    //     }
    // }
}
