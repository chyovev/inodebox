<?php

namespace App\Rules;

use App\Models\Inode;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InodeIsDirectoryRule implements ValidationRule
{

    ///////////////////////////////////////////////////////////////////////////
    public function __construct(private Inode $inode) {
        //
    }

    ///////////////////////////////////////////////////////////////////////////
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if ( ! $this->inode->isDirectory()) {
            $fail('validation.inode.not_directory')->translate();
        }
    }
}
