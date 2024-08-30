<?php

namespace App\Http\Requests\Api\Promo;

use App\Services\Error\MultiLanguageErrorService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePromoRequest extends FormRequest
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
            'code'              => ['required', Rule::unique('promos', 'code')->whereNull('deleted_at')],
            'description'       => 'nullable|min:1',
            'type'              => 'required|in:fixed,percentage',
            'duration'          => 'required|in:once,repeat',
            'amount'            => 'required|numeric|integer|gt:0',
            'is_active'         => 'required|boolean',
            'start_at'          => 'required|date|after_or_equal:today',
            'expired_at'        => 'required|date|after:start_at',
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
