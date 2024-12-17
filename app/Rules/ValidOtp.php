<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class ValidOtp implements ValidationRule
{
    public function __construct(
        private string $tableName,
        private string $otpColumn = 'otp',
        private string $expiredAtColumn = 'expired_at',
        )
    {
        
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $otp = DB::table($this->tableName)
                    ->where($this->otpColumn, $value)
                    ->where($this->expiredAtColumn, '>', now())
                    ->first();

        if ($otp == null) {
            $fail('Invalid OTP');
        }
    }
}
