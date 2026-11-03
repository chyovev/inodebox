<?php

namespace App\Validation\InputRules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InodeNameRule implements ValidationRule
{

    ///////////////////////////////////////////////////////////////////////////
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        // oversimplified regex to match only numbers and letters
        // (case insensitive) for base name potentially followed
        // by a lowercase/number extension separated by a dot
        $pattern = '/^[a-zA-Z0-9]+(\.[a-z0-9]+)?$/';

        if ( ! preg_match($pattern, $value)) {
            $fail('validation.inode.illegal_name')->translate();
        }
    }
}
