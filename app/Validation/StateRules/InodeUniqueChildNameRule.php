<?php

namespace App\Validation\StateRules;

use App\Models\Inode;

class InodeUniqueChildNameRule implements StateRule
{

    ///////////////////////////////////////////////////////////////////////////
    public function __construct(
        private Inode  $inode,
        private string $childName,
        private ?Inode $skipChild = null,
    ) {
        //
    }

    ///////////////////////////////////////////////////////////////////////////
    public function isValid(): bool {
        return $this->isNameUnique();
    }

    ///////////////////////////////////////////////////////////////////////////
    public function getMessage(): string {
        return __('validation.inode.name_already_taken', [
            'value' => $this->childName,
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////
    /**
     * Check if any of the direct children of a node shares the
     * same name as the specified one, regardless of the child's
     * type.
     * 
     * NB! When an inode gets renamed, it's the inode's parent's
     *     children which should be iterated, but the actual inode
     *     should be skipped (rejected) from the check as using
     *     its current name for a new name would otherwise lead to
     *     a false positive otherwise.
     */
    private function isNameUnique(): bool {
        return $this
            ->inode
            ->children
            ->reject(function(Inode $inode): bool {
                return $inode->id === $this->skipChild?->id;
            })
            ->filter(function(Inode $inode): bool {
                return $inode->name === $this->childName;
            })
            ->isEmpty();
    }
}
