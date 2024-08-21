<?php

namespace App\Http\Requests\Api\V1\Auth\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'old_password'  => 'required|string|current_password:sanctum',
            'new_password'  => 'required|min:8|string|confirmed',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $old_password      = $this->input('old_password');

            /** @var User */
            $user = auth()->user();

            if (!Hash::check($old_password, $user->password)) {
                
                $validator->errors()->add('old_password', 'Invalid old password');
            } 
        });
    }
}
