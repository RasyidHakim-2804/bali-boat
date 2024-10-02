<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\Profile\ResetPasswordUserProfileRequest;
use App\Http\Requests\Api\User\Profile\SetPasswordUserProfileRequest;
use App\Http\Requests\Api\User\Profile\UpdateUserLanguageRequest;
use App\Http\Requests\Api\User\Profile\UpdateUserProfileRequest;
use App\Http\Resources\Profile\UserProfileResource;
use App\Models\User;
use App\Services\Images\ImageService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

    public function update(UpdateUserProfileRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::where('id', request()->user()->id)->firstOrFail();

            $user->fill($request->validated());

            // if ($request->is_profile_picture_deleted == 1) $user = $this->deleteProfilePicture($user);

            if ($request->image) $user = $this->saveProfilePicture($user, $request->image);

            $emailChanges = true;

            if ($request->email != $user->email) $emailChanges = $this->createEmailChangesVerification($user, $request->email);

            if (!$emailChanges) return $this->errorMessage('Too many email changes. Email changes available after 1 month from your last email changes.');

            $user->save();

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage());
        }
    }

    public function changePassword(ResetPasswordUserProfileRequest $request)
    {
        $user = User::where('id', request()->user()->id)->firstOrFail();

        if ((!empty($user->password)) && (!$request->old_password))
            return $this->errorValidationResponse('old_password', 'Old Password is required.');

        if ((!empty($user->password)) && (!Hash::check($request->old_password, $user->password)))
            return $this->errorValidationResponse('old_password', 'Old Password is invalid.');

        if ((!empty($user->password)) && (Hash::check($request->password, $user->password)))
            return $this->errorValidationResponse('password', 'New password must be different with old password.');

        $user->password = bcrypt($request->password);

        $user->save();

        return $this->successStore();
    }

    public function setPassword(SetPasswordUserProfileRequest $request)
    {
        $user = User::where('id', request()->user()->id)->firstOrFail();

        $user->password = bcrypt($request->password);

        $user->save();

        return $this->successStore();
    }

    public function changeLanguage(UpdateUserLanguageRequest $request)
    {
        $user           = User::where('id', request()->user()->id)->firstOrFail();
        $user->language = $request->lang;
        
        $user->save();

        return $this->successStore();
    }

    protected function saveProfilePicture($user, $uploadedImage)
    {
        if (isset($user->profile_picture)) $this->deleteProfilePicture($user);
        
        $image = (new ImageService($user->imagePath(), $uploadedImage));

        $image->save();

        $user->profile_picture = $image->getImageName();

        return $user;
    }

    protected function deleteProfilePicture($user)
    {
        $image = (new ImageService($user->imagePath()));

        $image->delete($user->profile_picture);

        $user->profile_picture = null;

        return $user;
    }
}
