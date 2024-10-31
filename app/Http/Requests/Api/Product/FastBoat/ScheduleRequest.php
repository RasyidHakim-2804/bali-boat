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
                $rule['variants.' . $passenger . '.' . $age] = 'required|integer|min:0|max:1000000000';
            }
        }

        return $rule;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->isMethod('POST')) {
                $schedule = $this->route('fastBoat')
                            ->schedules()
                            ->where('departure_harbor_id', '=', $this->input('departure_harbor_id'))
                            ->where('arrival_harbor_id', '=', $this->input('arrival_harbor_id'))
                            ->where('departure_time', '=', $this->input('departure_time'))
                            ->where('arrival_time', '=', $this->input('arrival_time'))
                            ->first();

                if ($schedule != null) {
                    $validator->errors()->add('schedule', __('validation.duplicated_data', ['attribute' => 'schedule']));
                }
            }
        });
    }
}
