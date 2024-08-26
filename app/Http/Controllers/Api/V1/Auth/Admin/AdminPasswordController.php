<?php

namespace App\Http\Controllers\Api\V1\Auth\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\Admin\ChangePasswordRequest;
use App\Models\AdminResetPassword;
use App\Models\Admin;
use App\Http\Requests\Api\V1\Auth\Admin\ConfirmForgetPasswordRequest;
use App\Http\Requests\Api\V1\Auth\Admin\ForgetPasswordRequest;
use App\Http\Requests\Api\V1\Auth\Admin\VerifyOtpRequest;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use Illuminate\Support\Facades\Mail;
use App\Mail\Api\V1\ResetPasswordEmail;
use Illuminate\Support\Facades\DB;

class AdminPasswordController extends Controller
{
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        try {
            DB::beginTransaction();

            $admin = Admin::where('email', $request->email)->first();
            
            DB::commit();

            $this->sendForgetPassword($admin);

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
        $otp = AdminResetPassword::where('email', $request->email)->first();
            
        $otp->expired_at = now()->addMinutes(60);
        
        $otp->save();
        
        return new SuccessResource();
    }

    public function resetPassword(ConfirmForgetPasswordRequest $request)
    {
        DB::transaction(function() use ($request){
            $otp = AdminResetPassword::where('email', $request->email)->first();

            $admin           = Admin::where('email', $request->email)->first();
            $admin->password = $request->new_password;
            $admin->save();
            
            $this->deleteAllToken($admin);
            $otp->delete();    
        });

        return new SuccessResource(
            message: 'Your password has been changed'
        );
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        /** @var Admin */
        $admin           = auth()->user();
        $admin->password = $request->new_password;
        $admin->save();

        return new SuccessResource();
    }

    ///////////////////
    //private methods//
    ///////////////////

    private function sendForgetPassword(Admin $admin)
    { 
        $otp = $this->makeOtpResetPassword($admin);

        Mail::to($admin)->send(new ResetPasswordEmail(
            name: $admin->first_name,
            otp: $otp->otp
        ));
    }

    private function makeOtpResetPassword(Admin $admin): AdminResetPassword
    {
        $otp             = $admin->resetPassword()->firstOrNew();
        $otp->email      = $admin->email;
        $otp->otp        = rand(100000, 999999);
        $otp->expired_at = now()->addMinutes(1);
        $otp->save();

        return $otp;
    }

    /**
     * delete token from login
     */
    private function deleteAllToken(Admin $admin)
    {
        $admin->tokens()->delete();
    }
}
