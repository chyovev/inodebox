<?php

namespace App\Validation\StateRules;

use App\Models\Inode;

class InodeUniqueNameRule implements StateRule
{

    ///////////////////////////////////////////////////////////////////////////
    public function __construct(
        private Inode  $inode,
        private string $inodeName,
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
            'value' => $this->inodeName,
        ]);
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
    private function isNameUnique(): bool {
        return $this
            ->inode
            ->contents
            ->reject(function(Inode $inode): bool {
                return $inode->id === $this->skipChild?->id;
            })
            ->filter(function(Inode $inode): bool {
                return $inode->name === $this->inodeName;
            })
            ->isEmpty();
    }
}
