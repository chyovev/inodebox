<?php

namespace App\Actions;

use App\Enums\InodeTypeEnum;
use App\Exceptions\ValidationException;
use App\Models\Inode;
use App\Validation\StateRulePools\CreateFileStateRulePool;
use App\Validation\StateValidator;

class CreateFileAction
{

    ///////////////////////////////////////////////////////////////////////////
    public function __construct(
        private StateValidator $validator,
        private CreateFileStateRulePool $rulePool,
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
            'type' => InodeTypeEnum::FILE,
        ]);
    }

}
