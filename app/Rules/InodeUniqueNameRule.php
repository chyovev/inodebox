<?php

namespace App\Rules;

use App\Models\Inode;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InodeUniqueNameRule implements ValidationRule
{

    ///////////////////////////////////////////////////////////////////////////
    public function __construct(private Inode $inode) {
        //
    }

    ///////////////////////////////////////////////////////////////////////////
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if ($this->isNameTaken($value)) {
            $fail('validation.inode.name_already_taken')->translate([
                'value' => $value,
            ]);
        }
    }

    ///////////////////////////////////////////////////////////////////////////
    /**
     * Check if any of the direct children (contents) of a node
     * shares the same name as the specified one, regardless
     * of the node's type.
     */
    private function isNameTaken(string $name): bool {
        return $this
            ->inode
            ->contents
            ->filter(function(Inode $inode) use($name): bool {
                return $inode->name === $name;
            })
            ->count();
    }
}
