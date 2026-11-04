<?php

namespace Tests\Actions;

use App\Actions\ListContentsAction;
use App\Exceptions\ValidationException;
use App\Models\Inode;
use App\Validation\StateValidator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;
use Tests\TestCase;

class ListContentsActionTest extends TestCase
{

    use DatabaseTransactions;


    ///////////////////////////////////////////////////////////////////////////
    public function test_validation_failed(): void {
        $inode = Inode::factory()->file()->make();

        $this->expectException(ValidationException::class);

        $this->app->make(ListContentsAction::class)->execute($inode);
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_action_executed_successfully(): void {
        $inode = Inode::factory()->directory()->create();

        $this->mock(StateValidator::class, function(MockInterface $mock): void {
            $mock->shouldReceive('validate')->andReturnNull();
        });

        $this->assertFalse($inode->relationLoaded('children'));

        $this->app->make(ListContentsAction::class)->execute($inode);

        $this->assertTrue($inode->relationLoaded('children'));
    }

}