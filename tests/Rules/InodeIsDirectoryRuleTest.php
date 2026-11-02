<?php

namespace Tests\Rules;

use App\Models\Inode;
use App\Rules\InodeIsDirectoryRule;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class InodeIsDirectoryRuleTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_passes(): void {
        $inode = Inode::factory()->directory()->make();

        $this->assertSame([], $this->getValidationErrors($inode));
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_fails(): void {
        $inode = Inode::factory()->file()->make();

        $this->assertSame(['The target location is not a directory'], $this->getValidationErrors($inode));
    }

    ///////////////////////////////////////////////////////////////////////////
    private function getValidationErrors(Inode $inode): array {
        $rule      = new InodeIsDirectoryRule($inode);
        $attribute = 'parent';

        return Validator::make([$attribute => null], [$attribute => [$rule]])->errors()->all();
    }
}