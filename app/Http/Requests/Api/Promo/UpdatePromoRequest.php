<?php

namespace App\Http\Requests\Api\Promo;

use App\Services\Error\MultiLanguageErrorService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePromoRequest extends FormRequest
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
            'is_active'         => 'required|boolean',
            'is_new_user_promo' => ['required','boolean', Rule::unique('promos', 'is_new_user_promo')->where('is_new_user_promo', 1)->whereNull('deleted_at')->whereNot('id', $this->route('promo')->id)],
            'duration'          => 'required|in:once,repeat',
            'start_at'          => 'required|date',
            'expired_at'        => 'required|date|after:today',
        ];
    }

    public function messages()
    {
        $multiLangService = (new MultiLanguageErrorService);
        
        return [
            'is_new_user_promo.unique'  => $multiLangService->generate('New user promo', 'unique', config('app.locale')),
        ];
    }
}
