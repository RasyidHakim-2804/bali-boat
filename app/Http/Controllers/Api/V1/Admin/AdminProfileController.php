<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Profile\ResetPasswordAdminProfileRequest;
use App\Http\Requests\Api\Admin\Profile\UpdateAdminProfileRequest;
use App\Http\Requests\Api\User\Profile\SetPasswordUserProfileRequest;
use App\Http\Resources\Admin\AdminProfileResource;
use App\Mail\Admin\AdminEmailChangesVerificationMail;
use App\Models\Admin;
use App\Models\AdminEmailChangesVerificationToken;
use App\Services\Images\ImageService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminProfileController extends Controller
{
    use JsonResponsesTrait;

    public function index()
    {
        $admin = new AdminProfileResource(Admin::where('id', request()->user()->id)->firstOrFail());

        return $this->successGet($admin);
    }

    public function update(UpdateAdminProfileRequest $request)
    {
        $admin = Admin::where('id', request()->user()->id)->firstOrFail();

        $admin->fill($request->validated());
        
        if ($request->profile_picture) {
            if ($admin->profile_picture) $this->deleteProfilePicture($admin);

            $admin = $this->saveProfilePicture($admin, $request->profile_picture);
        }

        $admin->save();

        $emailChanges = true;

            if ($request->email != $admin->email) $emailChanges = $this->createEmailChangesVerification($admin, $request->email);

            if (!$emailChanges) return $this->errorMessage('Too many email changes. Email changes available after 1 month from your last email changes.');

        return $this->successStore();
    }

    public function changePassword(ResetPasswordAdminProfileRequest $request)
    {
        $admin = Admin::where('id', request()->user()->id)->firstOrFail();

        if (!Hash::check($request->old_password, $admin->password)) {
            return $this->errorValidationResponse('old_password', 'Old Password is invalid.');
        }

        if (Hash::check($request->password, $admin->password)) {
            return $this->errorValidationResponse('password', 'New password must be different with old password.');
        }

        $admin->password = bcrypt($request->password);

        $admin->save();

        return $this->successStore();
    }

    public function setPassword(SetPasswordUserProfileRequest $request)
    {
        $admin = Admin::where('id', request()->user()->id)->firstOrFail();

        $admin->password = bcrypt($request->password);

        $admin->save();

        return $this->successStore();
    }

    protected function createEmailChangesVerification($admin, $newEmail)
    {
        $lastEmailChanges = AdminEmailChangesVerificationToken::withTrashed()->where('agent_id', $admin->id)->orderBy('id', 'DESC')->first();

        if ($lastEmailChanges) {
            $timeLimitEmailChanges = date("Y-m-d H:i:s", strtotime("+1 month", strtotime($lastEmailChanges->created_at)));

            if (now() <= $timeLimitEmailChanges) return false;
        }

        $token = $this->generateToken();

        $emailChangesVerification             = new AdminEmailChangesVerificationToken();
        $emailChangesVerification->admin_id   = $admin->id;
        $emailChangesVerification->email      = $newEmail;
        $emailChangesVerification->token      = $token;
        $emailChangesVerification->created_at = now();

        $emailChangesVerification->save();

        $this->sendEmailEmailChangesVerification($admin->first_name, $newEmail, $token);

        return true;
    }

    protected function sendEmailEmailChangesVerification($firstName, $email, $token)
    {
        $data                                   = [];
        $data['first_name']                     = $firstName;
        $data['email_changes_verification_url'] = env('FRONTEND_URL') . '/admin/email-change-verification/' . $token;

        Mail::to($email)->send((new AdminEmailChangesVerificationMail($data)));
    }

    protected function saveProfilePicture($user, $uploadedImage)
    {
        if (!$uploadedImage) return $user;

        $image = (new ImageService($user->imagePath(), $uploadedImage));

        $image->save();

        $user->profile_picture = $image->getImageName();

        return $user;
    }

    protected function deleteProfilePicture($admin)
    {
        $image = (new ImageService($admin->imagePath()));

        $image->delete($admin->profile_picture);

        $admin->profile_picture = null;

        return $admin;
    }
}
