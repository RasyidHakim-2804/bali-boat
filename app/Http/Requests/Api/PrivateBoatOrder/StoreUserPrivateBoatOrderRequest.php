<?php

namespace App\Http\Requests\Api\PrivateBoatOrder;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class StoreUserPrivateBoatOrderRequest extends FormRequest
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
            'activity_date'     => 'required|date|after:yesterday',
            'return_date'       => [
                'nullable',
                'date',
                'after:yesterday',
                function ($attr, $value, $fail) {
                    $count = collect($this->input('products'))
                        ->reject(function ($product) {
                            return (!isset($product['id']));
                        })
                        ->count();
    
                    if ($count > 1) $fail(__('validation.required'));
                },
            ],
            'pic_name'          => 'required|min:1',
            'pic_email'         => 'required|min:1',
            'pic_phone_number'  => 'required|min:1',
            'promo_uuid'        => ['nullable', Rule::exists('promos', 'uuid')->where('is_active', 1)],
            'products'          => 'required|array',
            'products.*.id'     => [
                'required',
                function ($attr, $value, $fail) {
                    $count = collect($this->input('products'))
                        ->reject(function ($product) {
                            return isset($product['variant_id']);
                        })
                        ->reject(function ($product) {
                            return (!isset($product['id']));
                        })
                        ->filter(function ($variant) use ($value) {
                            return ($variant['id'] === $value);
                        })
                        ->count();
    
                    if ($count > 1) $fail(__('validation.distinct'));
                },
                Rule::exists('private_boats', 'id')->where('is_active', 1)->whereNull('deleted_at'),
            ],
            'products.*.variant_id' => [
                'required',
                function ($attr, $value, $fail) {
                    $count = collect($this->input('products'))
                        ->reject(function ($product) {
                            return (!isset($product['variant_id']));
                        })
                        ->filter(function ($products) use ($value) {
                            return (($products['variant_id'] === $value));
                        })
                        ->duplicates();
    
                    if ($count->count() > 0) $fail(__('validation.distinct'));
                },
                
                Rule::exists('private_boat_variants', 'id')->where('private_boat_id', $this->input('products.*.id'))->whereNull('deleted_at'),
            ],
            'products.*.quantity'       => 'required|integer|gt:0',
            'forms'                     => 'required|array',
            'forms.*.product_id'        => ['required',
                function ($attr, $value, $fail) {
                    $count = collect($this->input('products'))
                        ->filter(function ($products) use ($value) {
                            if (array_key_exists('id', $products)) return ($products['id'] === $value);
                        })
                        ->count();

                    if ($count < 1) $fail(__('validation.in'));
                },
            ],
            'forms.*.variant_id'        => [
                'nullable',
                function ($attr, $value, $fail) {
                    $count = collect($this->input('products'))
                        ->filter(function ($products) use ($value) {
                            return ((isset($products['variant_id'])) && ($products['variant_id'] === $value));
                        })
                        ->count();

                    if ($count < 1) $fail(__('validation.in'));
                },
                Rule::exists('fast_boat_schedule_variants', 'id')->where('fast_boat_schedule_id', $this->input('forms.*.product_id'))->whereNull('deleted_at'),
            ],
            'forms.*.name'              => 'required|string|min:1',
            'forms.*.nationality'       => 'required|min:1',
        ];
    }
}
