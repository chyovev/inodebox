<?php

namespace Tests\Validation\StateRules;

use App\Models\Inode;
use App\Validation\StateRules\InodeUniqueChildNameRule;
use Tests\TestCase;

class InodeUniqueChildNameRuleTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_valid(): void {
        $inode = Inode::factory()->directory()->make();

        $child1 = Inode::factory()->directory()->make(['name' => 'test1']);
        $child2 = Inode::factory()->file()     ->make(['name' => 'test2']);

        $inode->setRelation('children', collect([$child1, $child2]));

        $name = 'test';

        $this->assertTrue((new InodeUniqueChildNameRule($inode, $name))->isValid());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_invalid(): void {
        $inode = Inode::factory()->directory()->make();

        $child1 = Inode::factory()->directory()->make(['name' => 'test1']);
        $child2 = Inode::factory()->file()     ->make(['name' => 'test2']);

        $inode->setRelation('children', collect([$child1, $child2]));

        $name = 'test2';

        $this->assertFalse((new InodeUniqueChildNameRule($inode, $name))->isValid());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_message(): void {
        $inode = Inode::factory()->directory()->make();

        $name = 'test2';

        $this->assertSame('The specified value test2 is already taken', (new InodeUniqueChildNameRule($inode, $name))->getMessage());
    }

}