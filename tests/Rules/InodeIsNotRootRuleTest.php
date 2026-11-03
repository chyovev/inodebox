<?php

namespace Tests\Rules;

use App\Models\Inode;
use App\Rules\InodeIsNotRootRule;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class InodeIsNotRootRuleTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_passes(): void {
        $inode = Inode::factory()->directory()->make();

        $this->assertSame([], $this->getValidationErrors($inode));
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_fails(): void {
        $inode = Inode::factory()->root()->make();

        $this->assertSame(['You cannot alter the root directory'], $this->getValidationErrors($inode));
    }

    ///////////////////////////////////////////////////////////////////////////
    private function getValidationErrors(Inode $inode): array {
        $rule      = new InodeIsNotRootRule($inode);
        $attribute = 'parent';

        return Validator::make([$attribute => null], [$attribute => [$rule]])->errors()->all();
    }
}