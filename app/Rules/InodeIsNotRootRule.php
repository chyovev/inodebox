<?php

namespace App\Rules;

use App\Models\Inode;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InodeIsNotRootRule implements ValidationRule
{

    ///////////////////////////////////////////////////////////////////////////
    public function __construct(private Inode $inode) {
        //
    }

    ///////////////////////////////////////////////////////////////////////////
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if ($this->inode->isRoot()) {
            $fail('validation.inode.is_root')->translate();
        }
    }
}
