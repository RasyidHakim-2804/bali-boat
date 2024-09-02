<?php

namespace App\Http\Requests\Api\Include;

use App\Models\Language;
use App\Models\ProductInclude;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IncludeRequest extends FormRequest
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
        $rule = [
            'type'  => [
                'required',
                Rule::in(ProductInclude::$type),
            ]
        ];

        foreach (Language::code() as $lang) {
            $rule['description.' . $lang]    = 'required|string|max:200';
        }

        return $rule;
    }
}
