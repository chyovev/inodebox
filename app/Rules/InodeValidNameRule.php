<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InodeValidNameRule implements ValidationRule
{

    ///////////////////////////////////////////////////////////////////////////
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        // oversimplified regex to match only numbers and letters
        // (case insensitive) for base name potentially followed
        // by a lowercase extension separated by a dot
        $pattern = '/^[a-zA-Z0-9]+(\.[a-z]+)?$/';

        if ( ! preg_match($pattern, $value)) {
            $fail('validation.inode.illegal_name')->translate();
        }
    }
}
