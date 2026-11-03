<?php

namespace App\Rules;

use App\Models\Inode;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InodeUniqueNameRule implements ValidationRule
{

    ///////////////////////////////////////////////////////////////////////////
    public function __construct(private Inode $inode, private ?Inode $skipChild = null) {
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
     * 
     * NB! When an inode gets renamed, it's the inode's parent's
     *     contents which should be iterated, but the actual inode
     *     should be skipped (rejected) from the check as using
     *     its current name for a new name would lead to a false
     *     positive otherwise.
     */
    private function isNameTaken(string $name): bool {
        return $this
            ->inode
            ->contents
            ->reject(function(Inode $inode): bool {
                return $inode->id === $this->skipChild?->id;
            })
            ->filter(function(Inode $inode) use($name): bool {
                return $inode->name === $name;
            })
            ->count();
    }
}
