<?php

namespace App\Http\Requests\Api\User\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserProfileRequest extends FormRequest
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
            'first_name'    => 'required|min:1',
            'last_name'     => 'required|min:1',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2000',
            'email'         => [
                'required',
                'email:rfc,dns',
                Rule::unique('users', 'email')->ignore(request()->user()->id),
            ],
            'phone'         => 'nullable',
        ];
    }
}
