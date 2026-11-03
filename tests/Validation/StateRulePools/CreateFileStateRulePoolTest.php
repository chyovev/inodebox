<?php

namespace Tests\Validation\StateRulePools;

use App\Models\Inode;
use App\Validation\StateRulePools\CreateFileStateRulePool;
use App\Validation\StateRules\InodeIsDirectoryRule;
use App\Validation\StateRules\InodeUniqueChildNameRule;
use Tests\TestCase;

class CreateFileStateRulePoolTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_getter(): void {
        $inode    = Inode::factory()->directory()->make();
        $fileName = 'test.pdf';

        $rules = $this->app->make(CreateFileStateRulePool::class)->get($inode, $fileName);

        $this->assertSame([
            InodeIsDirectoryRule::class,
            InodeUniqueChildNameRule::class,
        ], array_map('get_class', $rules));
    }

}