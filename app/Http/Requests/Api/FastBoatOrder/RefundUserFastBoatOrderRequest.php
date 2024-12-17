<?php

namespace App\Http\Requests\Api\FastBoatOrder;

use Illuminate\Foundation\Http\FormRequest;

class RefundUserFastBoatOrderRequest extends FormRequest
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
            'channel_type'           => 'required|in:bank,ewallet',
            'channel_code'           => 'required|min:1',
            'channel_holder_name'    => 'required|min:1',
            'channel_account_number' => 'required|min:1',
        ];
    }
}
