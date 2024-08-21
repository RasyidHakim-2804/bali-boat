<?php

namespace App\Http\Requests\Api\Product\Activity;

use App\Models\Language;
use App\Models\ActivityItinerary;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityRequest extends FormRequest
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
        $arrayLanguage = 'array:';

        foreach (Language::code() as $lang) {
            $arrayLanguage = $arrayLanguage . $lang . ',';
        }

        $arrayLanguage = rtrim($arrayLanguage, ',');

        $rule = [
            'name'              => 'required|string|max:100',
            'price'             => 'required|numeric|min:0',
            'active'            => 'required|boolean',
            'location_id'       => 'required|exists:locations,id',
            'max_person'        => 'required|integer|min:1|max_digits:6',
            'tags'              => 'required|array',
            'tags.*'            => 'required|exists:tags,id',

            'highlight'         => ['required', $arrayLanguage],
            'highlight.*'       => 'required|string',

            'description'         => ['required', $arrayLanguage],
            'description.*'       => 'required|string',

            'includes'          => 'required|array',
            'includes.*'        => ['required', $arrayLanguage],
            'includes.*.*'      => 'required|string',

            'excludes'          => 'required|array',
            'excludes.*'        => ['required', $arrayLanguage],
            'excludes.*.*'      => 'required|string',

            'itinararies'                   => 'required|array',
            'itinararies.*'                 => ['required', $arrayLanguage],
            'itinararies.*.*'               => 'required|array:type,title,description',
            'itinararies.*.*.type'          => ['required','string', Rule::in(ActivityItinerary::TYPE)],
            'itinararies.*.*.title'         => 'required|string',
            'itinararies.*.*.description'   => 'required|string',
        ];

        foreach (Language::code() as $lang) {
            $rule['includes.*.' . $lang]    = 'required';
            $rule['excludes.*.' . $lang]    = 'required';
            $rule['itinararies.*.' . $lang] = 'required';
            $rule['highlight.' . $lang]     = 'required';
            $rule['description.' . $lang]   = 'required';
        }

        return $rule;
    }
}
