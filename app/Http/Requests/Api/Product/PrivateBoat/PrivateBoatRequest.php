<?php

namespace App\Http\Requests\Api\Product\PrivateBoat;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PrivateBoatRequest extends FormRequest
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
            'price'             => 'required|integer|min:0',
            'is_active'         => 'required|boolean',
            'rating'            => 'required|integer|min:0|max:5',
            'max_person'        => 'required|integer|min:1',
            'pickup_harbor_id'  => 'required|exists:harbors,id',
            'tags'              => 'required|array',

            'tags'              => 'required|array',
            'tags.*'            => [
                'required',
                Rule::exists('tags', 'id')->where('type', 'private-boat'),
            ],

            'includes'          => 'array|nullable',
            'includes.*'        => [
                'required',
                Rule::exists('product_includes', 'id')->where('type', 'private-boat'),
            ],

            'itineraries'       => 'array|nullable',
            'itineraries.*'     => [
                'required',
                Rule::exists('product_itineraries', 'id')->where('type', 'private-boat'),
            ],

            'highlight'         => ['required',$arrayLanguage],
            'highlight.*'       => ['required','string'],

            'meta'              => ['required',$arrayLanguage],
            'meta.*'            => ['required','string'],

            'specification'           => 'array',
            'specification.model'     => 'nullable|string',
            'specification.year'      => 'nullable|integer|digits:4|min:0',
            'specification.length'    => 'nullable|string',
            'specification.cabins'    => 'nullable|integer|min:1',
            'specification.heads'     => 'nullable|integer|min:1',
            'specification.engine'    => 'nullable|string',
        ];

        foreach (Language::code() as $lang) {
            $rule['highlight.' . $lang]     = 'required';
            $rule['meta.' . $lang]          = 'required';
        }

        return $rule;
    }
}
