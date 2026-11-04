<?php

namespace App\Validation\StateRules;

use App\Models\Inode;

class InodeUniqueChildNameRule implements StateRule
{

    ///////////////////////////////////////////////////////////////////////////
    public function __construct(private Inode  $inode, private string $childName) {
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
     */
    private function isNameUnique(): bool {
        return $this
            ->inode
            ->children
            ->filter(function(Inode $inode): bool {
                return $inode->name === $this->childName;
            })
            ->isEmpty();
    }
}
