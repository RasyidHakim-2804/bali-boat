<?php

namespace App\Http\Requests\Api\Tag;

use App\Models\Language;
use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagRequest extends FormRequest
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
                Rule::in(Tag::$type),
            ]
        ];

        foreach (Language::code() as $lang) {
            $rule['name.' . $lang]    = 'required|string|max:200';
        }

        return $rule;
    }
}
