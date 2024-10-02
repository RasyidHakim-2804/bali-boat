<?php

namespace App\Http\Requests\Api\Promo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyPromoRequest extends FormRequest
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
        return [
            'code' => [
                'required',
                'min:1',
                Rule::exists('promos', 'code')->where('is_active', 1)->where('deleted_at', null)
            ],
            'email' => ,
            'activity_date' => 'required|date|after_or_equal:today'
        ];
    }
}
