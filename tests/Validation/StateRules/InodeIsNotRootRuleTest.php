<?php

namespace Tests\Validation\StateRules;

use App\Models\Inode;
use App\Validation\StateRules\InodeIsNotRootRule;
use Tests\TestCase;

class InodeIsNotRootRuleTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_valid(): void {
        $inode = Inode::factory()->directory()->make();

        $this->assertTrue((new InodeIsNotRootRule($inode))->isValid());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_invalid(): void {
        $inode = Inode::factory()->root()->make();

        $this->assertFalse((new InodeIsNotRootRule($inode))->isValid());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_message(): void {
        $inode = Inode::factory()->root()->make();

        $this->assertSame('You cannot alter the root directory', (new InodeIsNotRootRule($inode))->getMessage());
    }

}