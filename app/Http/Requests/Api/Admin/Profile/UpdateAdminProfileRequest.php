<?php

namespace App\Http\Requests\Api\Admin\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminProfileRequest extends FormRequest
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
            'first_name'           => 'required|min:1',
            'last_name'            => 'required|min:1',
            'email'                => 'required|email:rfc,dns',
            'profile_picture'      => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2000',
        ];
    }
}
