<?php

namespace App\Http\Requests\Api\Admin\ForgetPassword;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordForgetAdminPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email'            => 'required|exists:admins,email',
            'pin'              => 'required|min:6|max:6',
            'password'         => 'required|min:8',
            'confirm_password' => 'required|same:password'
        ];
    }
}
