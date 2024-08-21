<?php

namespace App\Http\Requests\Api\V1\Auth\User;

use App\Models\UserResetPassword;
use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages()
    {
        return [
            'otp.exists'   => 'invalid Otp',
            'email.exists' => 'invalid Email',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:user_reset_passwords,email',
            'otp'   => 'required|numeric|digits:6|exists:user_reset_passwords,otp'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $email      = $this->input('email');
            $otp        = $this->input('otp');

            $reset = UserResetPassword::where('email', $email)
                    ->where('otp', $otp)
                    ->first();

            if ($reset == null) {
                
                $validator->errors()->add('email', 'Email and otp dont match');
            } elseif ($reset->otp < now()) {
                
                $validator->errors()->add('otp', 'Otp is expired');
            }
        });
    }
}
