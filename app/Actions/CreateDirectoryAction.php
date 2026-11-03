<?php

namespace App\Actions;

use App\Enums\InodeTypeEnum;
use App\Exceptions\ValidationException;
use App\Models\Inode;
use App\Validation\StateRulePools\CreateDirectoryStateRulePool;
use App\Validation\StateValidator;

class CreateDirectoryAction
{

    ///////////////////////////////////////////////////////////////////////////
    public function __construct(
        private StateValidator $validator,
        private CreateDirectoryStateRulePool $rulePool,
    ) {
        //
    }
    
    ///////////////////////////////////////////////////////////////////////////
    /**
     * @throws ValidationException
     */
    public function execute(Inode $parent, string $fileName): void {
        $this->validator->validate($this->rulePool->get($parent, $fileName));
        
        $parent->children()->create([
            'name' => $fileName,
            'type' => InodeTypeEnum::DIRECTORY,
        ]);
    }

}
