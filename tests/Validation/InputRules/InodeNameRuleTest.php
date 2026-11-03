<?php

namespace Tests\Validation\InputRules;

use App\Validation\InputRules\InodeNameRule;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class InodeNameRuleTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_passes_with_extension(): void {
        $name = 'Test1.jpg';

        $this->assertSame([], $this->getValidationErrors($name));
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_passes_with_no_extension(): void {
        $name = 'Test1jpg';

        $this->assertSame([], $this->getValidationErrors($name));
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_fails_with_extension(): void {
        $name = 'Test1.jpG';

        $this->assertSame(['The name field has illegal characters'], $this->getValidationErrors($name));
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_rule_fails_with_no_extension(): void {
        $name = 'Test1!';

        $this->assertSame(['The name field has illegal characters'], $this->getValidationErrors($name));
    }

    ///////////////////////////////////////////////////////////////////////////
    private function getValidationErrors(string $value): array {
        $rule      = new InodeNameRule;
        $attribute = 'name';

        return Validator::make([$attribute => $value], [$attribute => [$rule]])->errors()->all();
    }
}