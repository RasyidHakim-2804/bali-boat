<?php

namespace App\Http\Requests\Api\FaqCategory;

use App\Models\FaqCategory;
use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;

class FaqCategoryRequest extends FormRequest
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
        foreach (Language::code() as $lang) {
            $rule['title.' . $lang]         = 'required|string|max:200';
        }

        return $rule;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if ($this->isMethod('POST') && FaqCategory::count() >= 5) {
                $validator->errors()->add('category', 'categories already have a max number');
            }
        });
    }
}
