<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\Auth\AdminLoginRequest;
use App\Http\Requests\Api\Admin\Auth\ResendAdminEmailVerificationRequest;
use App\Mail\Admin\AdminRegisterVerificationMail;
use App\Mail\Admin\AdminWelcomeMail;
use App\Models\Admin;
use App\Models\AdminEmailVerificationToken;
use App\Notifications\Admin\AdminRegisterVerificationNotification;
use App\Notifications\Admin\AdminWelcomeNotification;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AuthAdminController extends Controller
{
    use JsonResponsesTrait;

    public function login(AdminLoginRequest $request)
    {
        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) return $this->errorValidationResponse('email', 'Username or password is invalid.');
        
        if (!Hash::check($request->password, $admin->password)) {
            return $this->errorValidationResponse('email', 'Username or password is invalid.');
        }

        if ($admin->is_active == 0) return $this->errorValidationResponse('email', 'Your account is blocked.');

        $token = $admin->createToken('admin', ['admin'])->accessToken;

        return $this->loginTokenResponse($token, 'ADMIN');
    }

    public function logout()
    {
        request()->user()->token()->revoke();

        return $this->successStore();
    }

    public function resendEmailVerification(ResendAdminEmailVerificationRequest $request)
    {
        DB::beginTransaction();

        try {
            $admin = Admin::where('email', $request->email)->first();

            AdminEmailVerificationToken::where('admin_id', $admin->id)->delete();

            $token = Str::random(255);

            $this->createEmailVerification($admin->id, $token);

            $this->sendEmail($admin, $token);

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function emailVerification(Request $request, $token)
    {
        $tokenVerification = AdminEmailVerificationToken::where('token', $token)->first();

        if (!$tokenVerification) return $this->errorValidationResponse(404, 'Token is invalid.');

        // $verificationDeadline = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($tokenVerification->created_at)));

        // if ($verificationDeadline < now()) return $this->errorValidationResponse('token', 'Token is expired.');
        
        DB::beginTransaction();
        try {
            $admin                    = $tokenVerification->admin;
            $admin->email_verified_at = date('Y-m-d H:i:s');
            $admin->is_active          = 1;

            $admin->save();

            $tokenVerification->delete();

            $token = $admin->createToken('admin', ['admin'])->accessToken;

            $this->sendWelcomeEmail($admin);

            DB::commit();

            return $this->loginTokenResponse($token, 'ADMIN');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorMessage($e->getMessage());
        }
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

    protected function sendWelcomeEmail($admin)
    {
        $data                        = [];
        $data['language']            = 'en';
        $data['first_name']          = $admin->first_name;
        $data['login_page_url']      = env('FRONTEND_URL') . '/sign-in';

        // Mail::to($admin->email)->send((new AdminWelcomeMail($data)));
        
        $admin->notify(new AdminWelcomeNotification($data));
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
}
