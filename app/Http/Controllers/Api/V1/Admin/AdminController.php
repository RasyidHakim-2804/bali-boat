<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreAdminRequest;
use App\Http\Requests\Api\Admin\UpdateAdminRequest;
use App\Http\Resources\Admin\AdminResource;
use App\Mail\Admin\AdminEmailChangesVerificationMail;
use App\Mail\Admin\AdminRegisterVerificationMail;
use App\Models\Admin;
use App\Models\AdminEmailChangesVerificationToken;
use App\Models\AdminEmailVerificationToken;
use App\Models\AdminPermission;
use App\Notifications\Admin\AdminEmailChangesVerificationNotification;
use App\Notifications\Admin\AdminRegisterVerificationNotification;
use App\Services\Admin\AdminPermissionService;
use App\Services\Images\ImageService;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AdminController extends Controller
{
    use JsonResponsesTrait;

    protected $permissionService;

    // public function __construct()
    // {
    //     $this->permissionService = (new AdminPermissionService('admins'));
    // }

    public function index()
    {
        // $this->permissionService->check('view');

        return AdminResource::collection(
            QueryBuilder::for(Admin::class)
            ->allowedFilters([
                AllowedFilter::scope('search'),
            ])
            ->where('id', '!=', 1)
            ->allowedSorts('name', 'id')
            ->paginate(25)
        );
    }

    public function show(Admin $admin)
    {
        // $this->permissionService->check('view');

        $admin = new AdminResource($admin);

        return $this->successGet($admin);
    }

    public function store(StoreAdminRequest $request, Admin $admin)
    {
        // $this->permissionService->check('full');

        $admin = new Admin();

        $admin->fill($request->validated());

        $admin->password            = bcrypt($request->password);
        $admin->uuid                = $this->generateUuid();
        $admin->is_active           = 0;
        $admin->email_verified_at   = now();

        $admin = $this->saveProfilePicture($admin, $request->image);

        $admin->save();

        $token = Str::random(255);

        $this->createEmailVerification($admin->id, $token);

        $this->sendEmail($admin, $token);

        if (isset($request->permissions)) $this->savePermission($request->permissions, $admin->id);

        return $this->successStore();
    }

    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        // $this->permissionService->check('full');

        $admin->fill($request->validated());

        $admin = $this->saveProfilePicture($admin, $request->image);

        $admin->save();

        if ($admin->email != $request->email) $this->createEmailChangesVerification($admin, $request->email);

        if (isset($request->permissions)) $this->savePermission($request->permissions, $admin->id);

        return $this->successStore();
    }

    public function destroy(Admin $admin)
    {
        // $this->permissionService->check('full');

        $admin->delete();

        $this->deleteProficePicture($admin);

        return $this->successStore();
    }

    protected function saveProfilePicture($admin, $uploadedImage)
    {
        if (!$uploadedImage) return $admin;

        $image = (new ImageService($admin->imagePath(), $uploadedImage));

        if ($admin->profile_picture) $image->delete($admin->profile_picture);

        $image->save();

        $admin->profile_picture = $image->getImageName();

        return $admin;
    }

    protected function deleteProficePicture($admin)
    {
        if (!$admin->profile_picture) return;

        $image = (new ImageService($admin->imagePath()));

        $image->delete($admin->profile_picture);

        return;
    }

    protected function generateUuid()
    {
        $sameUuid = 1;

        while ($sameUuid > 0) {
            $uuid     = Str::random(15);
            $sameUuid = Admin::where('uuid', $uuid)->count();
        }
        
        return $uuid;
    }

    protected function createEmailVerification(string $id, string $token)
    {
        $emailVerification             = new AdminEmailVerificationToken();
        $emailVerification->admin_id   = $id;
        $emailVerification->token      = $token;
        $emailVerification->created_at = now();

        $emailVerification->save();
    }

    protected function sendEmail($admin, $token)
    {
        $data                         = [];
        $data['language']             = 'en';
        $data['first_name']           = $admin->first_name;
        $data['email_validation_url'] = env('FRONTEND_URL') . '/admin/email-verification/' . $token;

        // Mail::to($admin->email)->send((new AdminRegisterVerificationMail($data)));
        $admin->notify(new AdminRegisterVerificationNotification($data));
    }

    protected function createEmailChangesVerification($admin, $newEmail)
    {
        $token = $this->generateEmailChangesToken();

        $emailChangesVerification             = new AdminEmailChangesVerificationToken();
        $emailChangesVerification->admin_id   = $admin->id;
        $emailChangesVerification->email      = $newEmail;
        $emailChangesVerification->token      = $token;
        $emailChangesVerification->created_at = now();

        $emailChangesVerification->save();

        // $this->sendEmailEmailChangesVerification($admin->first_name, $newEmail, $token);
        $this->sendNotificationEmailChangesVerification($admin, $token);

        return true;
    }

    protected function sendEmailEmailChangesVerification($firstName, $email, $token)
    {
        $data                                   = [];
        $data['first_name']                     = $firstName;
        $data['email_changes_verification_url'] = env('FRONTEND_URL') . '/admin/email-verification/' . $token;

        Mail::to($email)->send((new AdminEmailChangesVerificationMail($data)));
    }
    
    protected function sendNotificationEmailChangesVerification(Admin $admin, $token)
    {
        $url = env('FRONTEND_URL') . '/admin/email-verification/' . $token;

        $admin->notify(new AdminEmailChangesVerificationNotification($url, $admin->first_name));
    }

    protected function savePermission($permissions, $adminId)
    {
        $activePermissionId = collect($permissions)->whereNotNull('id')->pluck('id');

        AdminPermission::where('admin_id', $adminId)->whereNotIn('id', $activePermissionId)->delete();

        foreach ($permissions as $key => $permission) {
            $newPermission = null;

            if (array_key_exists('id', $permission)) $newPermission = AdminPermission::where('id', $permission['id'])->first();

            if (!$newPermission) $newPermission = new AdminPermission();
            
            $newPermission->admin_id    = $adminId;
            $newPermission->feature_id  = $permission['feature_id'];
            $newPermission->type        = $permission['type'];

            $newPermission->save();
        }
    }

    protected function deleteAllPermission($adminId)
    {
        AdminPermission::where('admin_id', $adminId)->delete();
    }
}
