<?php

namespace Tests\Validation\StateRulePools;

use App\Models\Inode;
use App\Validation\StateRulePools\ListContentsRulePool;
use App\Validation\StateRules\InodeIsDirectoryRule;
use Tests\TestCase;

class ListContentsRulePoolTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_getter(): void {
        $inode = Inode::factory()->file()->make();
        
        $rules = $this->app->make(ListContentsRulePool::class)->get($inode);

        $this->assertSame([
            InodeIsDirectoryRule::class,
        ], array_map('get_class', $rules));
    }

}