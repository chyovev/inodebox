<?php

namespace App\Actions;

use App\Exceptions\ValidationException;
use App\Models\Inode;
use App\Validation\StateRulePools\DeleteInodeStateRulePool;
use App\Validation\StateValidator;
use Illuminate\Support\Facades\DB;

class DeleteInodeAction
{

    ///////////////////////////////////////////////////////////////////////////
    public function __construct(
        private StateValidator $validator,
        private DeleteInodeStateRulePool $rulePool,
    ) {
        //
    }

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @throws ValidationException
     */
    public function execute(Inode $inode): void {
        $this->validator->validate($this->rulePool->get($inode));

        // child nodes and indices are automatically deleted on a database level,
        // no need to delete them explicitly via code
        DB::transaction(function() use($inode): void {
            $inode->delete();
        });
    }

}
