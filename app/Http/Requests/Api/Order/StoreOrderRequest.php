<?php

namespace App\Http\Requests\Api\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class StoreOrderRequest extends FormRequest
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
            'email'         => 'required|email:rfc,dns',
            'first_name'    => 'required|min:1',
            'last_name'     => 'required|min:1',
            'phone'         => 'required|min:1',
            'activity_date' => 'required|date|after:yesterday',
            'products'      => 'required|array',
            'products.*.id' => [
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
                Rule::exists('products', 'id')->where('is_saleable', 1)->whereNull('deleted_at'),
            ],
            'products.*.variant_id' => [
                'nullable',
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
                
                Rule::exists('product_variants', 'id')->where('product_id', $this->input('products.*.id'))->whereNull('deleted_at'),
            ],
            'products.*.quantity'   => 'required|integer|gt:0',

            'additionals'           => 'array',
            'additionals.*.id'      => [
                'required',
                function ($attr, $value, $fail) {
                    $count = collect($this->input('additionals'))
                        ->reject(function ($product) {
                            return (!isset($product['id']));
                        })
                        ->filter(function ($product) use ($value) {
                            return ($product['id'] === $value);
                        })
                        ->count();
    
                    if ($count < 1) $fail(__('validation.in'));
                },
                Rule::exists('product_additionals', 'id'),
            ],
            'additionals.*.quantity'    => 'required|integer|gt:0',

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
                Rule::exists('product_variants', 'id')->where('product_id', $this->input('forms.*.product_id'))->whereNull('deleted_at'),
            ],
            'forms.*.name'              => 'required|string|min:1',
            'forms.*.age'               => 'required|integer|gte:1|lte:200',
            'forms.*.phone'             => 'required_if:forms.*.is_pic,1|string|min:1',
            'forms.*.email'             => 'required_if:forms.*.is_pic,1|email:dns,rfc',
            'forms.*.is_pic'            => 'required|in:1,0',
        ];
    }
}
