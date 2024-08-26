<?php

namespace App\Http\Controllers\Api\V1\Auth\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\Admin\LoginRequest;
use App\Http\Requests\Api\V1\Auth\Admin\ResendEmailVerification;
use App\Http\Requests\Api\V1\Auth\Admin\VerifyEmailRequest;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Models\Admin;
use App\Service\Services\VerifyEmail\AdminVerifyEmailService;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function resendEmailVerification(ResendEmailVerification $request)
    {
        $admin        = Admin::where('email', $request->email)->firstOrFail();
        
        AdminVerifyEmailService::send($admin);

        return new SuccessResource();
    }

    public function verifyEmail(VerifyEmailRequest $request)
    {
        AdminVerifyEmailService::verifyEmail($request->token);

        return new SuccessResource(message:'Congrats, Your email has been verified');
    }
    
    public function login(LoginRequest $request)
    {
        /** @var Admin */
        $admin = Admin::where('email', $request->email)->first();
        
        if ($admin->id == 1) {
            $token = $admin->createToken('admin', ['is_admin', 'admin_master'],now()->addWeek())->plainTextToken;
            $role  = 'admin_master';
        } else {
            $token = $admin->createToken('admin', ['is_admin'], now()->addWeek())->plainTextToken;
            $role  = 'admin';
        }

        return response()->json([
            'status'      => 'success',
            'token'       => $token,
            'token-type'  => 'Bearer',
            'role'        => $role,
        ]);
    }

    public function logout(Request $request)
    {
        auth()->user()->currentAccessToken()->delete();
        return new SuccessResource();
    }    
    
}
