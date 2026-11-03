<?php

namespace App\Actions;

use App\Exceptions\ValidationException;
use App\Models\Inode;
use App\Validation\StateRulePools\RenameInodeStateRulePool;
use App\Validation\StateValidator;

class RenameInodeAction
{

    ///////////////////////////////////////////////////////////////////////////
    public function __construct(
        private StateValidator $validator,
        private RenameInodeStateRulePool $rulePool,
    ) {
        //
    }

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @throws ValidationException
     */
    public function execute(Inode $inode, string $inodeName): void {
        $this->validator->validate($this->rulePool->get($inode, $inodeName));

        $inode->update([
            'name' => $inodeName,
        ]);
    }

}
