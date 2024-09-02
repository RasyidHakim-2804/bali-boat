<?php

namespace App\Http\Controllers\Api\V1\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\Auth\ResendEmailVerificationRequest;
use App\Http\Requests\Api\User\Auth\UserLoginRequest;
use App\Http\Requests\Api\User\Auth\UserRegisterRequest;
use App\Models\User;
use App\Models\UserEmailVerificationToken;
use App\Notifications\User\UserRegisterVerificationNotification;
use App\Notifications\User\UserWelcomeNotification;
use App\Traits\JsonResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthUserController extends Controller
{
    use JsonResponsesTrait;

    public function login(UserLoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) return $this->errorValidationResponse('email', 'Username or password is invalid.');

        if (!$user->email_verified_at) return $this->errorValidationResponse('email', 'Email unverified.');
        
        if (!Hash::check($request->password, $user->password)) {
            return $this->errorValidationResponse('email', 'Username or password is invalid.');
        }

        if ($user->is_active == 0) return $this->errorValidationResponse('email', 'Your account is blocked.');

        $token = $user->createToken('user', ['user'])->accessToken;

        return $this->loginTokenResponse($token, 'USER');
    }

    public function logout()
    {
        request()->user()->token()->revoke();

        return $this->successStore();
    }

    public function register(UserRegisterRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = User::where('email', $request->email)->withTrashed()->first();

            if ($user) {
                UserEmailVerificationToken::where('user_id', $user->id)->delete();
                $user->deleted_at = null;
            }

            if (!$user) $user = new User();

            $user->password     = bcrypt($request->password);
            $user->uuid         = $this->generateUuid();
            $user->email        = $request->email;
            $user->is_active    = 0;
            
            $user->fill($request->validated());

            $user->save();

            $token = Str::random(255);

            $this->createEmailVerification($user->id, $token);

            $this->sendEmail($user, $token);

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function resendEmailVerification(ResendEmailVerificationRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = User::where('email', $request->email)->first();

            UserEmailVerificationToken::where('user_id', $user->id)->delete();

            $token = Str::random(255);

            $this->createEmailVerification($user->id, $token);

            $this->sendEmail($user, $token);

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
    }

    public function emailVerification(Request $request, $token)
    {
        $tokenVerification = UserEmailVerificationToken::where('token', $token)->first();

        if (!$tokenVerification) return $this->errorValidationResponse(404, 'Token is invalid.');

        // $verificationDeadline = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($tokenVerification->created_at)));

        // if ($verificationDeadline < now()) return $this->errorValidationResponse('token', 'Token is expired.');
        
        DB::beginTransaction();
        try {
            $user                    = $tokenVerification->user;
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->is_active         = 1;

            $user->save();

            $tokenVerification->delete();

            $token = $user->createToken('user')->accessToken;

            $this->sendWelcomeEmail($user);

            // $newUserPromo = Promo::where('is_new_user_promo', 1)->first();

            // if (($newUserPromo) && ($newUserPromo->start_new_user_promo_at <= $user->email_verified_at)) $this->sendEmailNewUserPromo($user, $newUserPromo);

            DB::commit();

            return $this->loginTokenResponse($token, 'USER');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorMessage($e->getMessage());
        }
    }

    protected function createEmailVerification(string $id, string $token)
    {
        $emailVerification             = new UserEmailVerificationToken();
        $emailVerification->user_id    = $id;
        $emailVerification->token      = $token;
        $emailVerification->created_at = now();

        $emailVerification->save();
    }

    protected function sendEmail($user, $token)
    {
        $data                           = [];
        $data['language']               = config('app.locale');
        $data['first_name']             = $user->first_name;
        $data['email_verification_url'] = env('FRONTEND_URL') . '/email-verification/' . $token;

        $user->notify(new UserRegisterVerificationNotification($data));
    }

    protected function sendWelcomeEmail($user)
    {
        $data                        = [];
        $data['language']            = config('app.locale');
        $data['first_name']          = $user->first_name;
        $data['login_page_url']      = env('FRONTEND_URL') . '/login';

        $user->notify(new UserWelcomeNotification($data));
    }

    protected function generateUuid()
    {
        $sameUuid = 1;

        while ($sameUuid > 0) {
            $uuid     = Str::random(15);
            $sameUuid = User::where('uuid', $uuid)->count();
        }
        
        return $uuid;
    }
}
