<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\ForgetPassword\ForgetAdminPasswordRequest;
use App\Http\Requests\Api\Admin\ForgetPassword\ResetPasswordForgetAdminPasswordRequest;
use App\Http\Requests\Api\Admin\ForgetPassword\VerifyForgetAdminPasswordPinRequest;
use App\Mail\Admin\ForgetAdminPasswordMail;
use App\Models\Admin;
use App\Models\ForgetAdminPasswordReset;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ForgetAdminPasswordController extends Controller
{
    use JsonResponsesTrait;

    public function forgetPassword(ForgetAdminPasswordRequest $request)
    {
        $admin      = Admin::where('email', $request->email)->first();
        $existed    = ForgetAdminPasswordReset::where('admin_id', $admin->id)->first();
        $expireTime = null;

        if ($existed) $expireTime  = date("Y-m-d H:i:s", strtotime("+1 minutes", strtotime($existed->created_at)));
        
        if (($expireTime) && ($expireTime > now())) return $this->errorMessage('Please wait 1 minute after the last pin request.');

        DB::beginTransaction();
        try {
            if ($admin->is_active == 0) return $this->errorValidationResponse('email', 'Your account is blocked.');

            $pin   = $this->genereatePin($admin);

            $this->deleteForgetPasswordPins($admin);

            $forgetPasswordReset             = new ForgetAdminPasswordReset();
            $forgetPasswordReset->admin_id   = $admin->id;
            // $forgetPasswordReset->expired_at = Carbon::now()->addMinutes(5);
            $forgetPasswordReset->pin        = $pin;

            $forgetPasswordReset->save();

            $this->sendEmail($admin, $pin);

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
        
    }

    public function verifyPin(VerifyForgetAdminPasswordPinRequest $request)
    {
        $admin = Admin::where('email', $request->email)->first();

        if (!$this->pinValidation($admin, $request->pin)) return $this->errorInvalidPin();

        return $this->successStore();
    }

    public function resetPassword(ResetPasswordForgetAdminPasswordRequest $request)
    {
        $admin = Admin::where('email', $request->email)->first();

        if (!$this->pinValidation($admin, $request->pin)) return $this->errorInvalidPin();

        $admin->password = bcrypt($request->password);

        $admin->save();

        $this->deleteForgetPasswordPins($admin);

        return $this->successStore();
    }

    protected function pinValidation($admin, $pin)
    {
        $pin = $admin->forgetPasswordResets()->where('pin', $pin)->first();

        if (!$pin) return false;

        $expired_date = date('Y-m-d H:i:s', strtotime($pin->created_at . ' +1 day'));

        return (now() <= $expired_date);
    }

    protected function deleteForgetPasswordPins($admin)
    {
        if ($admin->forgetPasswordResets) {
            foreach ($admin->forgetPasswordResets as $key => $forgetPasswordReset) {
                $forgetPasswordReset->delete();
            }
        }
    }

    protected function genereatePin($admin)
    {
        do {
            $pin = rand(000000, 999999);
            $samePin = ForgetAdminPasswordReset::where('pin', $pin)->where('admin_id', $admin->id)->count();
        } while (($samePin > 0) || (strlen($pin) != 6));

        return $pin;
    }

    public function sendEmail($admin, $pin)
    {
        $data               = [];
        $data['pin']        = $pin;
        $data['email']      = $admin->email;
        $data['first_name'] = $admin->first_name;

        Mail::to($admin->email)->send((new ForgetAdminPasswordMail($data)));
    }

    protected function errorInvalidPin()
    {
        return response()->json([
            'message' => 'Pin is invalid.',
            'errors'  => [
                'pin' => ['Pin is invalid.']
            ]
        ], 422);
    }
}
