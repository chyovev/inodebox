<?php

namespace Tests\Actions;

use App\Actions\CreateFileAction;
use App\Actions\RenameInodeAction;
use App\Exceptions\ValidationException;
use App\Models\Inode;
use App\Validation\StateValidator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class RenameInodeActionTest extends TestCase
{

    use DatabaseTransactions;


    ///////////////////////////////////////////////////////////////////////////
    public function test_validation_failed(): void {
        $inode   = Inode::factory()->root()->make();
        $dirName = 'test';

        $this->expectException(ValidationException::class);

        $this->app->make(RenameInodeAction::class)->execute($inode, $dirName);
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_action_executed_successfully(): void {
        $parent  = Inode::factory()->directory()->create();
        $inode = Inode::factory()->for($parent, 'parent')->file()->create([
            'name' => 'test',
        ]);

        $inodeName = 'test2';

        $this->assertSame('test', $inode->name);

        $this->mock(StateValidator::class, function(MockInterface $mock): void {
            $mock->shouldReceive('validate')->andReturnNull();
        });

        $this->app->make(RenameInodeAction::class)->execute($inode, $inodeName);

        $this->assertSame('test2', $inode->name);
    }

}