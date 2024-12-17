<?php

namespace App\Http\Requests\Api\User\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRegisterRequest extends FormRequest
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
            'email'            => ['required','email:rfc,dns', Rule::unique('users', 'email')->whereNotNull('email_verified_at')],
            'first_name'       => 'required|min:1',
            'last_name'        => 'required|min:1',
            'phone'            => 'required|string|max:20',
            'password'         => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ];
    }
}
