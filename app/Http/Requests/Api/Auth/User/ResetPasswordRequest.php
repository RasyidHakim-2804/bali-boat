<?php

namespace App\Http\Requests\Api\V1\Auth\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\EmailVerified;

class ResetPasswordRequest extends FormRequest
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
            'email.exists' => 'The email address has not registered.'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $verified = new EmailVerified('users');
        return [
            'email' => ['bail','required','email','max:255','exists:users,email', $verified]
        ];
    }
}
