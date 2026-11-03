<?php

namespace App\Actions;

use App\Enums\InodeTypeEnum;
use App\Events\InodeCreated;
use App\Exceptions\ValidationException;
use App\Models\Inode;
use App\Validation\StateRulePools\CreateDirectoryStateRulePool;
use App\Validation\StateValidator;
use Illuminate\Support\Facades\DB;

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
    public function execute(Inode $parent, string $dirName): void {
        $this->validator->validate($this->rulePool->get($parent, $dirName));
        
        DB::transaction(function() use($parent, $dirName): void {
            $inode = new Inode([
                'name' => $dirName,
                'type' => InodeTypeEnum::DIRECTORY,
            ]);

            $inode->parent()->associate($parent);
            $inode->save();

            InodeCreated::dispatch($inode);
        });
    }

}
