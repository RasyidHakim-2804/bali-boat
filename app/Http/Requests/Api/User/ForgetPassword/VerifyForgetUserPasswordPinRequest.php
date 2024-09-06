<?php

namespace App\Http\Requests\Api\User\ForgetPassword;

use Illuminate\Foundation\Http\FormRequest;

class VerifyForgetUserPasswordPinRequest extends FormRequest
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
            'email'     => 'required|exists:users,email',
            'pin'       => 'required|min:6|max:6'
        ];
    }
}
