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
            'price'             => 'required|numeric|min:0',
            'active'            => 'required|boolean',
            'pickup_harbor_id'  => 'required|exists:harbors,id',
            'tags'              => 'required|array',
            'is_rented'         => 'required|boolean',
            'is_promo'          => 'required|boolean',

            'tags'              => 'required|array',
            'tags.*'            => 'required|exists:tags,id',
            'includes'          => 'required|array',
            'includes.*'        => 'required|exists:product_includes,id',

            'highlight'         => ['required',$arrayLanguage],
            'highlight.*'       => ['required','string'],

            'meta'              => ['required',$arrayLanguage],
            'meta.*'            => ['required','string'],

            'specification'             => ['required', $arrayLanguage],
            'specification.*.model'     => 'required|string',
            'specification.*.year'      => 'required|string',
            'specification.*.length'    => 'required|string',
            'specification.*.cabins'    => 'required|string',
            'specification.*.heads'     => 'required|string',
            'specification.*.engine'    => 'required|string',
        ];

        foreach (Language::code() as $lang) {
            $rule['specification.' . $lang] = 'required';
            $rule['highlight.' . $lang]     = 'required';
            $rule['meta.' . $lang]          = 'required';
        }

        return $rule;
    }
}
