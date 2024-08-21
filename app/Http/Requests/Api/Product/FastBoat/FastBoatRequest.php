<?php

namespace App\Http\Requests\Api\Product\FastBoat;

use Illuminate\Foundation\Http\FormRequest;

class FastBoatRequest extends FormRequest
{
    private $ruleImage = 'required|mimes:png,jpg|max:2048';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if (strtolower($this->method()) == 'put') {
            $this->ruleImage = 'nullable|mimes:png,jpg|max:2048';
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'   => 'required|string|max:255',
            'active' => 'required|boolean',
            'rating' => 'required|numeric|min:0|max:5',
            'image'  => $this->ruleImage,
        ];
    }
}
