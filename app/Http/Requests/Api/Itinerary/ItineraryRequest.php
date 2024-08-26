<?php

namespace App\Http\Requests\Api\Itinerary;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;

class ItineraryRequest extends FormRequest
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
        foreach (Language::code() as $lang) {
            $rule['description.' . $lang]   = 'required|string|max:200';
            $rule['title.' . $lang]         = 'required|string|max:200';
        }
        
        if (strtolower($this->method()) == 'put') {
            
            $rule['image'] = 'nullable|image|mimes:png,jpg|max:2048';

        } else {

            $rule['image'] = 'required|image|mimes:png,jpg|max:2048';
        }

        return $rule;
    }
}
