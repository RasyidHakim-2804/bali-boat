<?php

namespace App\Http\Requests\Api\Article;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
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
            'title'         => 'required|array',
            'title.en'      => 'required|min:1',
            'title.id'      => 'required|min:1',
            'type'          => 'required|in:travel,about_bali',
            'tag'           => 'required|array',
            'tag.en'        => 'required|min:1',
            'tag.id'        => 'required|min:1',
            'body'          => 'required|array',
            'body.en'       => 'required|min:1',
            'body.id'       => 'required|min:1',
            'meta'          => 'required|array',
            'meta.en'       => 'required|min:1',
            'meta.id'       => 'required|min:1',
            'thumbnail'     => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2000',
        ];
    }
}
