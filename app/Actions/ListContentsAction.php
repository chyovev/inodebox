<?php

namespace App\Actions;

use App\Exceptions\ValidationException;
use App\Models\Inode;
use App\Validation\StateRulePools\ListContentsRulePool;
use App\Validation\StateValidator;

class ListContentsAction
{

    ///////////////////////////////////////////////////////////////////////////
    public function __construct(
        private StateValidator $validator,
        private ListContentsRulePool $rulePool,
    ) {
        //
    }

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @throws ValidationException
     */
    public function execute(Inode $inode): void {
        $this->validator->validate($this->rulePool->get($inode));

        $inode->load(['children', 'recursiveParents']);
    }

}
