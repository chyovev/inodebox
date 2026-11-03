<?php

namespace App\Validation\StateRulePools;

use App\Models\Inode;
use App\Validation\StateRules\InodeIsNotRootRule;
use App\Validation\StateRules\StateRule;

class DeleteInodeStateRulePool
{

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @return StateRule[]
     */
    public function get(Inode $inode): array {
        return [
            new InodeIsNotRootRule($inode),
        ];
    }

}