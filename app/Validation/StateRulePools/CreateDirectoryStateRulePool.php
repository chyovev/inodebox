<?php

namespace App\Validation\StateRulePools;

use App\Models\Inode;
use App\Validation\StateRules\InodeIsDirectoryRule;
use App\Validation\StateRules\InodeUniqueChildNameRule;
use App\Validation\StateRules\StateRule;

class CreateDirectoryStateRulePool
{

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @return StateRule[]
     */
    public function get(Inode $inode, string $dirName): array {
        return [
            new InodeIsDirectoryRule($inode),
            new InodeUniqueChildNameRule($inode, $dirName),
        ];
    }

}