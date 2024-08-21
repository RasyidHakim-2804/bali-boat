<?php

namespace App\Http\Requests\Api\V1\Auth\Admin;

use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'token' => $this->segment(count($this->segments()))
        ]);
    }

    public function messages()
    {
        return [
            'token.exists' => 'invalid token'
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
            'token' => 'required|string|exists:admin_verify_emails,token'
        ];
    }
}
