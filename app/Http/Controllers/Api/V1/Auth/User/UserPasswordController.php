<?php

namespace App\Http\Controllers\Api\V1\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\User\ChangePasswordRequest;
use App\Models\UserResetPassword;
use App\Models\User;
use App\Http\Requests\Api\V1\Auth\User\ResetPasswordRequest;
use App\Http\Requests\Api\V1\Auth\User\ConfirmResetPasswordRequest;
use App\Http\Requests\Api\V1\Auth\User\VerifyOtpRequest;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Mail\Api\V1\ResetPasswordEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UserPasswordController extends Controller
{
    public function forgetPassword(ResetPasswordRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = User::where('email', $request->email)->first();

            DB::commit();

            $this->sendForgetPassword($user);

            return new SuccessResource(
                message: 'OTP has been sent to your email'
            );

        } catch (\Throwable $th) {

            DB::rollBack();
            
            throw $th;
        }
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $otp = UserResetPassword::where('email', $request->email)->first();
        
        $otp->expired_at = now()->addMinutes(60);
        
        $otp->save();
        
        return new SuccessResource();
    }

    public function resetPassword(ConfirmResetPasswordRequest $request)
    {
        DB::transaction(function() use ($request){
            $otp = UserResetPassword::where('email', $request->email)->first();
        
            $user           = User::where('email', $request->email)->first();
            $user->password = $request->new_password;
            $user->save();
            
            $this->deleteAllToken($user);
            $otp->delete();    
        });

        return new SuccessResource(
            message: 'Your password has been changed'
        );
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        /** @var User */
        $user           = auth()->user();
        $user->password = $request->new_password;
        $user->save();

        return new SuccessResource();
    }

    ///////////////////
    //private methods//
    ///////////////////

    private function sendForgetPassword(User $user)
    { 
        $otp = $this->makeOtpResetPassword($user);

        Mail::to($user)->send(new ResetPasswordEmail(
            name: $user->first_name,
            otp: $otp->otp
        ));
    }

    private function makeOtpResetPassword(User $user): UserResetPassword
    {
        $otp             = $user->resetPassword()->firstOrNew();
        $otp->email      = $user->email;
        $otp->otp        = rand(100000, 999999);
        $otp->expired_at = now()->addMinutes(1);
        $otp->save();

        return $otp;
    }

    /**
     * delete token from login
     */
    private function deleteAllToken(User $user)
    {
        $user->tokens()->delete();
    }
}
