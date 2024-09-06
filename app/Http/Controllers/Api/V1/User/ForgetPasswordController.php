<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\ForgetPassword\ForgetUserPasswordRequest;
use App\Http\Requests\Api\User\ForgetPassword\ResetPasswordForgetUserPasswordRequest;
use App\Http\Requests\Api\User\ForgetPassword\VerifyForgetUserPasswordPinRequest;
use App\Mail\User\ForgetUserPasswordMail;
use App\Models\User;
use App\Models\ForgetUserPasswordReset;
use App\Notifications\User\UserForgetPasswordPinNotification;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ForgetPasswordController extends Controller
{
    use JsonResponsesTrait;

    public function forgetPassword(ForgetUserPasswordRequest $request)
    {
        $user       = User::where('email', $request->email)->first();
        $existed    = ForgetUserPasswordReset::where('user_id', $user->id)->first();
        $expireTime = null;

        if ($existed) $expireTime  = date("Y-m-d H:i:s", strtotime("+1 minutes", strtotime($existed->created_at)));
        
        if (($expireTime) && ($expireTime > now())) return $this->errorMessage('Please wait 1 minute after the last pin request.');

        DB::beginTransaction();
        try {
            if ($user->is_active == 0) return $this->errorValidationResponse('email', 'Your account is blocked.');

            $pin  = $this->genereatePin($user);

            $this->deleteForgetPasswordPins($user);

            $forgetPasswordReset             = new ForgetUserPasswordReset();
            $forgetPasswordReset->user_id    = $user->id;
            // $forgetPasswordReset->expired_at = Carbon::now()->addMinutes(5);
            $forgetPasswordReset->pin        = $pin;

            $forgetPasswordReset->save();

            $this->sendEmail($user, $pin);

            DB::commit();

            return $this->successStore();
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorServer($e->getMessage());
        }
        
    }

    public function verifyPin(VerifyForgetUserPasswordPinRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$this->pinValidation($user, $request->pin)) return $this->errorInvalidPin();

        return $this->successStore();
    }

    public function resetPassword(ResetPasswordForgetUserPasswordRequest $request)
    {
        $talent = User::where('email', $request->email)->first();

        if (!$this->pinValidation($talent, $request->pin)) return $this->errorInvalidPin();

        $talent->password = bcrypt($request->password);

        $talent->save();

        $this->deleteForgetPasswordPins($talent);

        return $this->successStore();
    }

    protected function pinValidation($user, $pin)
    {
        $pin = $user->forgetPasswordResets()->where('pin', $pin)->first();

        if (!$pin) return false;

        $expired_date = date('Y-m-d H:i:s', strtotime($pin->created_at . ' +1 day'));

        return (now() <= $expired_date);
    }

    protected function deleteForgetPasswordPins($user)
    {
        if ($user->forgetPasswordResets) {
            foreach ($user->forgetPasswordResets as $key => $forgetPasswordReset) {
                $forgetPasswordReset->delete();
            }
        }
    }

    protected function genereatePin($user)
    {
        do {
            $pin = rand(000000, 999999);
            $samePin = ForgetUserPasswordReset::where('pin', $pin)->where('user_id', $user->id)->count();
        } while (($samePin > 0) || (strlen($pin) != 6));

        return $pin;
    }

    public function sendEmail($user, $pin)
    {
        $data               = [];
        $data['language']   = config('app.locale');
        $data['pin']        = $pin;
        $data['email']      = $user->email;
        $data['first_name'] = $user->first_name;

        $user->notify(new UserForgetPasswordPinNotification($data));
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
