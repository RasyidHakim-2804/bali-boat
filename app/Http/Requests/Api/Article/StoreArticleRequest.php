<?php

namespace App\Http\Requests\Api\Article;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
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
            'title'         => 'required|min:1',
            'type'          => 'required|in:travel,about_bali',
            'tag'           => 'required|min:1',
            'body'          => 'required|array',
            'body.en'       => 'required|min:1',
            'body.id'       => 'required|min:1',
            'meta'          => 'required|array',
            'meta.en'       => 'required|min:1',
            'meta.id'       => 'required|min:1',
            'thumbnail'     => 'required|image|mimes:jpeg,png,jpg,svg|max:2000',
        ];
    }
}
