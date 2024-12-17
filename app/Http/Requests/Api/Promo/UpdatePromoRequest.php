<?php

namespace App\Http\Requests\Api\Promo;

use App\Services\Error\MultiLanguageErrorService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePromoRequest extends FormRequest
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
            'is_active'         => 'required|boolean',
            'duration'          => 'required|in:once,repeat',
            'start_at'          => 'required|date',
            'expired_at'        => 'required|date|after:today',
            'for_start_date'    => 'required_if:is_all_date,0|date|after_or_equal:today',
            'for_end_date'      => 'required_if:is_all_date,0|date|after:for_start_date',
            'days'              => 'required_if:is_all_day,0|array',
            'days.*'            => 'required|distinct:strict|min:1|max:7|numeric',
        ];
    }
}
