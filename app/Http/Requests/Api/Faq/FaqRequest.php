<?php

namespace App\Http\Requests\Api\Faq;

use App\Models\Faq;
use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
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
    public function rules(): array
    {
        foreach (Language::code() as $lang) {
            $rule['question.' . $lang] = 'required|string|max:100';
            $rule['answer.' . $lang]   = 'required|string|max:300';
        }

        $rule['category_id'] = 'required|exists:faq_categories,id';
        $rule['is_top']      = 'required|bool';

        return $rule;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $faq = Faq::where('is_top', '=', 1)->count();

            if ($faq == 10) {
                $validator->errors()->add('faq', __('validation.max.numeric', [
                    'attribute' => 'top FAQ',
                    'max'       => 10
                ]));
            }
        });
    }
}
