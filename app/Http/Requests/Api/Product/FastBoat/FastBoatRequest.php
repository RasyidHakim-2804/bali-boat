<?php

namespace App\Http\Requests\Api\Product\FastBoat;

use Illuminate\Foundation\Http\FormRequest;

class FastBoatRequest extends FormRequest
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
        if (strtolower($this->method()) == 'put') {
            $ruleImage = 'nullable|mimes:png,jpg|max:2048';
        } else {
            $ruleImage = 'required|mimes:png,jpg|max:2048';
        }

        return [
            'name'      => 'required|string|max:255',
            'is_active' => 'required|boolean',
            'image'     => $ruleImage,
            'rating'    => 'required|numeric|min:0|max:5'
        ];
    }
}
