<?php

namespace App\Actions;

use App\Enums\InodeTypeEnum;
use App\Events\InodeCreated;
use App\Exceptions\ValidationException;
use App\Models\Inode;
use App\Validation\StateRulePools\CreateFileStateRulePool;
use App\Validation\StateValidator;
use Illuminate\Support\Facades\DB;

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

        DB::transaction(function() use ($parent, $fileName): void {
            $inode = new Inode([
                'name' => $fileName,
                'type' => InodeTypeEnum::FILE,
            ]);

            $inode->parent()->associate($parent);
            $inode->save();

            InodeCreated::dispatch($inode);
        });
    }

}
