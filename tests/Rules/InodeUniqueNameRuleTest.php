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

        $child1 = Inode::factory()->directory()->make(['name' => 'test1']);
        $child2 = Inode::factory()->file()     ->make(['name' => 'test2']);

        $inode->setRelation('contents', collect([$child1, $child2]));

        $name = 'test';

        $this->assertSame([], $this->getValidationErrors($inode, $name));
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_fails(): void {
        $inode = Inode::factory()->directory()->make();

        $child1 = Inode::factory()->directory()->make(['id' => 101, 'name' => 'test1']);
        $child2 = Inode::factory()->file()     ->make(['id' => 102, 'name' => 'test2']);

        $inode->setRelation('contents', collect([$child1, $child2]));

        $name = 'test2';

        $this->assertSame(['The specified value test2 for parent field is already taken'], $this->getValidationErrors($inode, $name));
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_passes_with_skipped_child(): void {
        $inode = Inode::factory()->directory()->make();

        $child1 = Inode::factory()->directory()->make(['id' => 101, 'name' => 'test1']);
        $child2 = Inode::factory()->file()     ->make(['id' => 102, 'name' => 'test2']);

        $inode->setRelation('contents', collect([$child1, $child2]));

        $name = 'test2';

        $this->assertSame(['The specified value test2 for parent field is already taken'], $this->getValidationErrors($inode, $name));
    }

    ///////////////////////////////////////////////////////////////////////////
    private function getValidationErrors(Inode $inode, string $value, ?Inode $skipNode = null): array {
        $rule      = new InodeUniqueNameRule($inode, $skipNode);
        $attribute = 'parent';

        return Validator::make([$attribute => $value], [$attribute => [$rule]])->errors()->all();
    }
}