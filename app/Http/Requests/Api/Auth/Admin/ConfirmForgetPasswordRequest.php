<?php

namespace App\Http\Requests\Api\V1\Auth\Admin;

use App\Models\AdminResetPassword;
use Illuminate\Foundation\Http\FormRequest;

class ConfirmForgetPasswordRequest extends FormRequest
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
            'email.exists' => 'invalid email address',
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
            'email'         => 'bail|required|email|exists:admin_reset_passwords|email',
            'otp'           => 'bail|required|numeric|digits:6|exists:admin_reset_passwords,otp',
            'new_password'  => 'bail|required|min:8|confirmed'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $email      = $this->input('email');
            $otp        = $this->input('otp');

            $reset = AdminResetPassword::where('email', $email)
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
