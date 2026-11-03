<?php

namespace Tests\Validation\StateRulePools;

use App\Models\Inode;
use App\Validation\StateRulePools\DeleteInodeStateRulePool;
use App\Validation\StateRules\InodeIsNotRootRule;
use Tests\TestCase;

class DeleteInodeStateRulePoolTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_getter(): void {
        $inode = Inode::factory()->file()->make();
        
        $rules = $this->app->make(DeleteInodeStateRulePool::class)->get($inode);

        $this->assertSame([
            InodeIsNotRootRule::class,
        ], array_map('get_class', $rules));
    }

}