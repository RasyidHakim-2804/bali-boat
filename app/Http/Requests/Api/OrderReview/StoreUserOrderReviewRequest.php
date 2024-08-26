<?php

namespace App\Http\Requests\Api\OrderReview;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserOrderReviewRequest extends FormRequest
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
            'order_id'  => ['required', Rule::exists('orders', 'id')->where('user_id', request()->user()->id)],
            'rate'      => 'required|gte:1|lte:5',
            'review'    => 'required|min:1',
        ];
    }
}
