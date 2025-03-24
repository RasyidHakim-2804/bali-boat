<?php

namespace App\Http\Requests\Api\Product\Activity;

use App\Models\ActivityTranslation;
use App\Models\Language;
use App\Models\ActivityVariant;
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
        $in   = 'in:';
        $code = Language::code();

        foreach ($code as $lang) {
            $in = $in . $lang . ',';
        }

        $in = rtrim($in, ',');

        $rule = [
            'name'              => 'required|string|max:100',
            'is_active'         => 'required|boolean',
            'rating'            => 'required|integer|min:0|max:5',
            'location_id'       => [
                'required',
                Rule::exists('locations', 'id')->where('deleted_at', null)
            ],
            'max_person'        => 'required|integer|min:1|max_digits:6',

            'tags'              => 'required|array',
            'tags.*'            => [
                'required',
                Rule::exists('tags', 'id')->where('type', 'activity'),
            ],

            'highlight'         => ['array'],
            'highlight.*'       => ['string',$in],

            'description'       => ['array'],
            'description.*'     => ['string',$in],

            'meta'              => ['required', 'array'],
            'meta.*'            => ['required','string', $in],

            'includes'          => 'required|array',
            'includes.*'        => [
                'required',
                Rule::exists('product_includes', 'id')->where('type', 'activity'),
            ],

            'excludes'          => 'required|array',
            'excludes.*'        => [
                'required',
                Rule::exists('product_excludes', 'id')->where('type', 'activity'),
            ],

            'itineraries'       => 'required|array',
            'itineraries.*'     => [
                'required',
                Rule::exists('product_itineraries', 'id')->where('type', 'activity'),
            ],
        ];

        foreach ($code as $lang) {
            $rule['highlight.' . $lang]     = 'nullable';
            $rule['description.' . $lang]   = 'nullable';
            $rule['meta.' . $lang]          = 'required';
        }

        foreach (ActivityVariant::PASSENGER_TYPE as $type) {
            foreach (ActivityVariant::NAME as $name) {
                $rule['variants.' . $type . '.' . $name] = 'required|integer|min:0|max:1000000000';
            }
        }

        return $rule;
    }
}
