<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminRequest extends FormRequest
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
            'first_name'            => 'required|min:1',
            'last_name'             => 'required|min:1',
            'email'                 => ['required','email:rfc,dns', Rule::unique('admins', 'email')->whereNot('id', $this->route('admin')->id)],
            'is_active'             => 'required|boolean',
            'image'                 => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2000',
            // 'permissions'           => 'required|array',
            // 'permissions.*.id'        => ['nullable', Rule::exists('admin_permissions', 'id')->where('admin_id', $this->route('admin')->id)],
            // 'permissions.*.feature_id'=> 'required|exists:admin_features,id|distinct',
            // 'permissions.*.type'      => 'required|in:full,view',
        ];
    }
}
