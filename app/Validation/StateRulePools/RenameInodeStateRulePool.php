<?php

namespace App\Validation\StateRulePools;

use App\Models\Inode;
use App\Validation\StateRules\InodeIsNotRootRule;
use App\Validation\StateRules\InodeUniqueChildNameRule;
use App\Validation\StateRules\StateRule;

class RenameInodeStateRulePool
{

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @return StateRule[]
     */
    public function get(Inode $inode, string $newName): array {
        $rules = [
            new InodeIsNotRootRule($inode),
        ];

        // the unique rule cannot be executed on the root inode
        // as it's the only one without a parent, but renaming
        // it is not allowed anyway as it's considered reserved;
        if ( ! $inode->isRoot()) {
            $rules[] = new InodeUniqueChildNameRule($inode->parent, $newName, $inode);
        }

        return $rules;
    }

}