<?php

namespace App\Http\Requests\Api\Product\PrivateBoat;

use App\Models\PrivateBoatImage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SavePrivateBoatImageRequest extends FormRequest
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
        return [
            'images_id'      => 'array',
            'images_id.*'    => [
                'required',
                'numeric',
                'distinct',
                'min:1',
                Rule::exists(PrivateBoatImage::class, 'id')->where(function($query){
                    $query->where('rental_id', $this->route('rental')->id);
                })
            ],
        ];
    }
}
