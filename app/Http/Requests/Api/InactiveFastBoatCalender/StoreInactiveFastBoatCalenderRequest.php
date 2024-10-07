<?php

namespace App\Http\Requests\Api\InactiveFastBoatCalender;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInactiveFastBoatCalenderRequest extends FormRequest
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
            'product_id'    => 'required|exists:fast_boat_schedules,id',
            'month'         => 'required|numeric|gte:1|lte:12',
            'year'          => 'required|numeric|digits:4',
            'dates'         => 'nullable|array',
            'dates.*'       => ['required','date_format:Y-m-d','after_or_equal:today'],
        ];
    }
}
