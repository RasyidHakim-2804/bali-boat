<?php

namespace App\Http\Requests\Api\InactivePrivateBoatCalender;

use Illuminate\Foundation\Http\FormRequest;

class TopPrivateBoatDateRequest extends FormRequest
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
            'date'  => ['required','date_format:Y-m-d','after_or_equal:today']
        ];
    }
}
