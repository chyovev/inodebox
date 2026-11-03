<?php

namespace Tests\Validation\StateRules;

use App\Models\Inode;
use App\Validation\StateRules\InodeIsDirectoryRule;
use Tests\TestCase;

class InodeIsDirectoryRuleTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_valid(): void {
        $inode = Inode::factory()->directory()->make();

        $this->assertTrue((new InodeIsDirectoryRule($inode))->isValid());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_invalid(): void {
        $inode = Inode::factory()->file()->make();

        $this->assertFalse((new InodeIsDirectoryRule($inode))->isValid());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_message(): void {
        $inode = Inode::factory()->file()->make();

        $this->assertSame('The target location is not a directory', (new InodeIsDirectoryRule($inode))->getMessage());
    }

}