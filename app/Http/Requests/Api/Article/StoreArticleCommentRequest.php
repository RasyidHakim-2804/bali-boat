<?php

namespace App\Http\Requests\Api\Article;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleCommentRequest extends FormRequest
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
            'comment'     => 'required|min:1',
            'name'        => 'required|min:1',
            'email'       => 'required|email',
        ];
    }
}
