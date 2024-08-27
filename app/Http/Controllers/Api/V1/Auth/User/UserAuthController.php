<?php

namespace App\Http\Controllers\Api\V1\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\User\LoginRequest;
use App\Http\Requests\Api\V1\Auth\User\RegisterRequest;
use App\Http\Requests\Api\V1\Auth\User\ResendEmailVerification;
use App\Http\Requests\Api\V1\Auth\User\VerifyEmailRequest;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Mail\Api\V1\VerifyEmail;
use App\Models\User;
use App\Models\UserVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserAuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();
            $user              = new User();
            $user->uuid        = Str::ulid(now())->toBase58(); 
            $user->first_name  = $request->first_name;
            $user->last_name   = $request->last_name;
            $user->email       = $request->email;
            $user->password    = Hash::make($request->password);
    
            $user->save();
    
            DB::commit();

            $this->sendEmailVerification($user);
    
            return new SuccessResource(
                code: 201,
                message: 'Your account has been created, please check your email for verification'
            );        
    
        } catch (\Throwable $th) {
            
            DB::rollBack();
            
            throw $th;
        }
    }

    public function resendEmailVerification(ResendEmailVerification $request)
    {
        $user        = User::where('email', $request->email)->firstOrFail();
        
        $this->sendEmailVerification($user);

        return new SuccessResource(
            code: 200,
            message: 'Verified email has been resended'
        );
    }

    public function verifyEmail(VerifyEmailRequest $request)
    {
        DB::transaction(function() use ($request) {
            $verify                  = UserVerifyEmail::where('token', $request->token)->first();

            $user                    = User::where('email', $verify->email)->firstOrFail();
            $user->email_verified_at = now();
            $user->save();
            
            $verify->delete();    
        });

        return new SuccessResource(message:'Congrats, Your email has been verified');
    }

    public function login(LoginRequest $request)
    {
        /** @var User*/
        $user = User::where('email', $request->email)->firstOrFail();
        
        $token = $user->createToken('user', ['is_user'])->plainTextToken;

        return response()->json([
            'status'      => 'success',
            'token'       => $token,
            'token-type'  => 'Bearer',
            'role'        => 'user'
        ]);
    }

    public function logout(Request $request)
    {
        auth()->user()->currentAccessToken()->delete();
        return new SuccessResource(
            code: 200,
            message: 'You have been logged out'
        );
    }

    
    
    ///////////////////
    //private methods//
    ///////////////////

    private function sendEmailVerification(User $user)
    {
        $verifyEmail = UserVerifyEmail::where('user_id', $user->id)->first();
        $token       = null;

        if ($verifyEmail !== null) {
            $token = $verifyEmail->token;
        } else {
            $token = $this->createVerifyToken($user);
        }

        Mail::to($user)->send(new VerifyEmail(
            name: $user->first_name,
            token: $token,
            prefix: 'users'
        ));
        // $user->notify(new VerifyEmailNotification($user, $token, 'users'));
    }

    private function createVerifyToken(User $user)
    {
        $verifyEmail            = new UserVerifyEmail();
        $verifyEmail->user_id   = $user->id;
        $verifyEmail->email     = $user->email;
        $verifyEmail->token     = str_replace(['.', '/', '\\'],'', Hash::make(now()));
        $verifyEmail->save();

        return $verifyEmail->token;
    }
}
