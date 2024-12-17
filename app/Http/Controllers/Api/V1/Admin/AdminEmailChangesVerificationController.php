<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Mail\Admin\AdminEmailChangesSuccessMail;
use App\Models\Admin;
use App\Models\AdminEmailVerificationToken;
use App\Notifications\Admin\AdminEmailChangesSuccessNotification;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\Mail;

class AdminEmailChangesVerificationController extends Controller
{
    use JsonResponsesTrait;

    public function __invoke(AdminEmailVerificationToken $token)
    {
        $admin = Admin::where('id', $token->agent_id)->first();

        $admin->email = $token->email;

        $admin->save();

        $token->delete();

        // $this->sendEmailAdminEmailChangesSuccess($admin->first_name, $admin->email);

        $admin->notify(new AdminEmailChangesSuccessNotification($admin)); 

        return $this->successStore();
    }

    protected function sendEmailAdminEmailChangesSuccess($admin)
    {
        $data                = [];
        $data['first_name']  = $admin->first_name;

        $admin->notify(new AdminEmailChangesSuccessNotification($data));

        // Mail::to($email)->send((new AdminEmailChangesSuccessMail($data)));
    }
}
