<?php

namespace App\Http\Requests\Api\V1\Auth\Admin;

use App\Rules\EmailVerified;
use Illuminate\Foundation\Http\FormRequest;

class ResendEmailVerification extends FormRequest
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
        $verified = new EmailVerified('admins', false);
        return [
            'email' => ['bail','required','email','exists:admins,email', $verified]
        ];
    }
}
