<?php

namespace App\Validation\StateRulePools;

use App\Models\Inode;
use App\Validation\StateRules\InodeIsDirectoryRule;
use App\Validation\StateRules\StateRule;

class ListContentsRulePool
{

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @return StateRule[]
     */
    public function get(Inode $inode): array {
        return [
            new InodeIsDirectoryRule($inode),
        ];
    }

}