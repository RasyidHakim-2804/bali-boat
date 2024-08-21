<?php

namespace App\Http\Requests\Api\V1\Auth\Admin;

use App\Rules\EmailVerified;
use App\Rules\IsActive;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class LoginRequest extends FormRequest
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
            'exists' => 'Invalid email or password'
        ];
    }

    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;
    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isVerified = new EmailVerified('admins');
        $isActive   = new IsActive('admins');
        return [
            'email'     => ['bail','required','email','max:50', $isVerified, $isActive],
            'password'  => 'required|string|min:8',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $email      = $this->input('email');
            $password   = $this->input('password');

            $admin = Admin::where('email', $email)->first();

            if (!$admin || !Hash::check($password, $admin->password)) {
                $validator->errors()->add('email', 'Invalid email or password');
            }
        });
    }

}
