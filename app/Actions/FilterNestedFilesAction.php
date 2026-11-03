<?php

namespace App\Actions;

use App\Exceptions\ValidationException;
use App\Models\Inode;
use App\Validation\StateRulePools\FilterNestedFilesStateRulePool;
use App\Validation\StateValidator;
use Illuminate\Support\Collection;

class FilterNestedFilesAction
{

    ///////////////////////////////////////////////////////////////////////////
    public function __construct(
        private StateValidator $validator,
        private FilterNestedFilesStateRulePool $rulePool,
    ) {
        //
    }

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @throws ValidationException
     * @return Collection<Inode>
     */
    public function execute(Inode $inode, string $search): Collection {
        $this->validator->validate($this->rulePool->get($inode));

        return $inode
            ->recursiveChildren()
            ->files()
            ->where('name', 'LIKE', "{$search}%")
            ->get();
    }

}
