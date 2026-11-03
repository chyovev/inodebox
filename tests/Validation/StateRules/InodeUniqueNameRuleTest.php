<?php

namespace Tests\Validation\StateRules;

use App\Models\Inode;
use App\Validation\StateRules\InodeUniqueNameRule;
use Tests\TestCase;

class InodeUniqueNameRuleTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_valid(): void {
        $inode = Inode::factory()->directory()->make();

        $child1 = Inode::factory()->directory()->make(['name' => 'test1']);
        $child2 = Inode::factory()->file()     ->make(['name' => 'test2']);

        $inode->setRelation('contents', collect([$child1, $child2]));

        $name = 'test';

        $this->assertTrue((new InodeUniqueNameRule($inode, $name))->isValid());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_valid_with_skipped_child(): void {
        $inode = Inode::factory()->directory()->make();

        $child1 = Inode::factory()->directory()->make(['id' => 101, 'name' => 'test1']);
        $child2 = Inode::factory()->file()     ->make(['id' => 102, 'name' => 'test2']);

        $inode->setRelation('contents', collect([$child1, $child2]));

        $name = 'test2';

        $this->assertTrue((new InodeUniqueNameRule($inode, $name, $child2))->isValid());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_invalid(): void {
        $inode = Inode::factory()->directory()->make();

        $child1 = Inode::factory()->directory()->make(['id' => 101, 'name' => 'test1']);
        $child2 = Inode::factory()->file()     ->make(['id' => 102, 'name' => 'test2']);

        $inode->setRelation('contents', collect([$child1, $child2]));

        $name = 'test2';

        $this->assertFalse((new InodeUniqueNameRule($inode, $name))->isValid());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_message(): void {
        $inode = Inode::factory()->directory()->make();

        $name = 'test2';

        $this->assertSame('The specified value test2 is already taken', (new InodeUniqueNameRule($inode, $name))->getMessage());
    }

}