<?php 
namespace App\Service\Services\VerifyEmail;

use App\Mail\Api\V1\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\AdminVerifyEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminVerifyEmailService
{
    public static function send(Admin $admin)
    {
        $verifyEmail = AdminVerifyEmail::where('admin_id', $admin->id)->first();
        $token       = null;

        if ($verifyEmail !== null) {
            $token = $verifyEmail->token;
        } else {
            $token = self::createVerifyToken($admin);
        }

        Mail::to($admin)->send(new VerifyEmail(
            name: $admin->first_name,
            token: $token,
            prefix: 'admins'
        ));
    }
    

    public static function verifyEmail($token)
    {
        DB::transaction(function () use ($token){
            
            $verify = AdminVerifyEmail::where('token', $token)->first();

            $admin                      = Admin::where('email', $verify->email)->firstOrFail();
            $admin->active              = 1;
            $admin->email_verified_at   = now();
            $admin->save();
            
            $verify->delete();    
        });
    }

    ///////////////////
    //private methods//
    ///////////////////


    private static function createVerifyToken(Admin $admin)
    {
        $verifyEmail = new AdminVerifyEmail();
        $verifyEmail->admin_id  = $admin->id;
        $verifyEmail->email     = $admin->email;
        $verifyEmail->token     = str_replace(['.', '/', '\\'],'', Hash::make(now()));
        $verifyEmail->save();

        return $verifyEmail->token;
    }

    
}