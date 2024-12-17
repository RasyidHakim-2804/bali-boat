<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
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
            'first_name'                => 'required|min:1',
            'last_name'                 => 'required|min:1',
            'email'                     => 'required|unique:admins,email',
            'profile_picture'           => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2000',
            'password'                  => 'required|min:8',
            'confirm_password'          => 'required|same:password',
            // 'permissions'               => 'nullable|array',
            // 'permissions.*.feature_id'  => 'required|exists:admin_features,id|distinct',
            // 'permissions.*.type'        => 'required|in:full,view',
        ];
    }
}
