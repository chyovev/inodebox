<?php

namespace Tests\Rules;

use App\Models\Inode;
use App\Rules\InodeUniqueNameRule;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class InodeUniqueNameRuleTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_passes(): void {
        $inode = Inode::factory()->directory()->make();
        $inode->setRelation('contents', collect([
            Inode::factory()->directory()->make(['name' => 'test1']),
            Inode::factory()->file()->make(['name' => 'test2']),
        ]));

        $name = 'test';

        $this->assertSame([], $this->getValidationErrors($inode, $name));
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_fails(): void {
        $inode = Inode::factory()->directory()->make();
        $inode->setRelation('contents', collect([
            Inode::factory()->directory()->make(['name' => 'test1']),
            Inode::factory()->file()->make(['name' => 'test2']),
        ]));

        $name = 'test2';

        $this->assertSame(['The specified value test2 for parent field is already taken'], $this->getValidationErrors($inode, $name));
    }

    ///////////////////////////////////////////////////////////////////////////
    private function getValidationErrors(Inode $inode, string $value): array {
        $rule      = new InodeUniqueNameRule($inode);
        $attribute = 'parent';

        return Validator::make([$attribute => $value], [$attribute => [$rule]])->errors()->all();
    }
}