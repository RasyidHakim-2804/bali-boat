<?php

namespace App\Http\Requests\Api\Product\Activity;

use App\Models\ActivityImage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveActivityImageRequest extends FormRequest
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
            'images_id'      => 'array|required|max:5',
            'images_id.*'    => [
                'required',
                'numeric',
                'distinct',
                'min:1',
                Rule::exists(ActivityImage::class, 'id')->where(function($query){
                    $query->where('activity_id', $this->route('activity')->id);
                })
            ],
        ];
    }
}
