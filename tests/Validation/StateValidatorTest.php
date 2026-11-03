<?php

namespace Tests\Validation;

use App\Exceptions\ValidationException;
use App\Validation\StateRules\StateRule;
use App\Validation\StateValidator;
use Mockery\MockInterface;
use Tests\TestCase;

class StateValidatorTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_validation_passes(): void {
        $rule = $this->mock(StateRule::class, function(MockInterface $mock): void {
            $mock->shouldReceive('isValid')->once()->andReturn(true);
        });

        $this->app->make(StateValidator::class)->validate([$rule]);

        $this->addToAssertionCount(1);
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_validation_fails(): void {
        $rule = $this->mock(StateRule::class, function(MockInterface $mock): void {
            $mock->shouldReceive('isValid')->once()->andReturn(false);
            $mock->shouldReceive('getMessage')->once()->andReturn('Lorem ipsum dolor');
        });

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Lorem ipsum dolor');

        $this->app->make(StateValidator::class)->validate([$rule]);
    }

}