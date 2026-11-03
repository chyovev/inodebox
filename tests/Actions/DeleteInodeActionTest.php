<?php

namespace Tests\Actions;

use App\Actions\DeleteInodeAction;
use App\Exceptions\ValidationException;
use App\Models\Inode;
use App\Validation\StateValidator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class DeleteInodeActionTest extends TestCase
{

    use DatabaseTransactions;


    ///////////////////////////////////////////////////////////////////////////
    public function test_validation_failed(): void {
        $inode = Inode::factory()->root()->make();

        $this->expectException(ValidationException::class);

        $this->app->make(DeleteInodeAction::class)->execute($inode);
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_action_executed_successfully(): void {
        $inode = Inode::factory()->directory()->create();
        Inode::factory()->for($inode, 'parent')->file()->create();

        $this->mock(StateValidator::class, function(MockInterface $mock): void {
            $mock->shouldReceive('validate')->andReturnNull();
        });

        $this->assertSame(1, Inode::where('id',        $inode->id)->count());
        $this->assertSame(1, Inode::where('parent_id', $inode->id)->count());

        $this->app->make(DeleteInodeAction::class)->execute($inode);

        $this->assertSame(0, Inode::where('id',        $inode->id)->count());
        $this->assertSame(0, Inode::where('parent_id', $inode->id)->count());
    }

}