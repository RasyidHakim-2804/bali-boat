<?php

namespace App\Http\Requests\Api\V1\Auth\User;

use App\Models\User;
use App\Rules\EmailVerified;
use App\Rules\IsActive;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;
    
    public function messages()
    {
        return [
            'exists' => 'Invalid email or password'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isVerified = new EmailVerified('users');
        $isActive   = new IsActive('users');
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

            // Memeriksa apakah email ada dalam tabel users
            $user = User::where('email', $email)->first();

            if (!$user || !Hash::check($password, $user->password)) {
                $validator->errors()->add('email', 'Invalid email or password');
            }
        });
    }
}
