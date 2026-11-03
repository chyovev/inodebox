<?php

namespace Tests\Validation\StateRulePools;

use App\Models\Inode;
use App\Validation\StateRulePools\RenameInodeStateRulePool;
use App\Validation\StateRules\InodeIsNotRootRule;
use App\Validation\StateRules\InodeUniqueNameRule;
use Tests\TestCase;

class RenameInodeStateRulePoolTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_getter_regular_inode(): void {
        $parent  = Inode::factory()->directory()->make();
        $inode   = Inode::factory()->file()->make();
        $inode->parent()->associate($parent);
        
        $newName = 'test';

        $rules = $this->app->make(RenameInodeStateRulePool::class)->get($inode, $newName);

        $this->assertSame([
            InodeIsNotRootRule::class,
            InodeUniqueNameRule::class,
        ], array_map('get_class', $rules));
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_getter_root_inode(): void {
        $inode   = Inode::factory()->root()->make();
        $newName = 'test';

        $rules = $this->app->make(RenameInodeStateRulePool::class)->get($inode, $newName);

        $this->assertSame([
            InodeIsNotRootRule::class,
        ], array_map('get_class', $rules));
    }

}