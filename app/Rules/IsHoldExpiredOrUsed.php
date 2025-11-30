<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Hold;

class IsHoldExpiredOrUsed implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $hold = Hold::find($value);
        if ($hold && $hold->is_expired) {
            $fail('Hold is expired.');
        }
        if ($hold && $hold->is_used) {
            $fail('Hold is used.');
        }
    }
}
