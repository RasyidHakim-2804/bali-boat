<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

/**
 * check email verified
 */
class EmailVerified implements ValidationRule
{
    public function __construct(private string $tableName,private bool $isVerified = true)
    {
        
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = DB::table($this->tableName)
                        ->select('email_verified_at')
                        ->where('email', $value)
                        ->first();
                        
        if ($user != null) {
            if ($user->email_verified_at == null && $this->isVerified == true) {
                $fail('Email not verified');
            }
    
            if ($user->email_verified_at != null && $this->isVerified == false) {
                $fail('You have already verified your email');
            }
        }
    }
}
