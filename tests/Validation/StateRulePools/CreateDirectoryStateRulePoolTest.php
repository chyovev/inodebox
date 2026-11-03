<?php

namespace Tests\Validation\StateRulePools;

use App\Models\Inode;
use App\Validation\StateRulePools\CreateDirectoryStateRulePool;
use App\Validation\StateRules\InodeIsDirectoryRule;
use App\Validation\StateRules\InodeUniqueChildNameRule;
use Tests\TestCase;

class CreateDirectoryStateRulePoolTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_getter(): void {
        $inode   = Inode::factory()->directory()->make();
        $dirName = 'test';

        $rules = $this->app->make(CreateDirectoryStateRulePool::class)->get($inode, $dirName);

        $this->assertSame([
            InodeIsDirectoryRule::class,
            InodeUniqueChildNameRule::class,
        ], array_map('get_class', $rules));
    }

}