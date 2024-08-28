<?php

namespace App\Http\Requests\Api\Product\FastBoat;

use App\Models\FastBoatScheduleVariant;
use Illuminate\Foundation\Http\FormRequest;

class ScheduleRequest extends FormRequest
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
        $rule = [
            'departure_harbor_id' => 'required|exists:harbors,id',
            'arrival_harbor_id'   => 'required|exists:harbors,id|different:departure_harbor_id',
            'departure_time'      => 'required|date_format:H:i',
            'arrival_time'        => 'required|date_format:H:i|after:departure_time',
            'is_active'           => 'required|boolean'
        ];

        foreach (FastBoatScheduleVariant::PASSENGER_TYPE as $passenger) {
            foreach (FastBoatScheduleVariant::AGE_TYPE as $age) {
                $rule['variants.' . $passenger . '.' . $age] = 'required|numeric|min:0|max:1000000000';
            }
        }

        return $rule;
    }
}
