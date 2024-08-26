<?php

namespace App\Http\Requests\Api\Exclude;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;

class ExcludeRequest extends FormRequest
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
        foreach (Language::code() as $lang) {
            $rule['description.' . $lang]    = 'required|string|max:200';
        }

        return $rule;
    }
}
