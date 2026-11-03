<?php

namespace Tests\Actions;

use App\Actions\CreateDirectoryAction;
use App\Exceptions\ValidationException;
use App\Models\Inode;
use App\Validation\StateValidator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class CreateDirectoryActionTest extends TestCase
{

    use DatabaseTransactions;

    ///////////////////////////////////////////////////////////////////////////
    public function test_validation_failed(): void {
        $inode   = Inode::factory()->file()->create();
        $dirName = 'test';

        $this->expectException(ValidationException::class);

        $this->app->make(CreateDirectoryAction::class)->execute($inode, $dirName);
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_action_executed_successfully(): void {
        $inode    = Inode::factory()->directory()->create();
        $children = $inode->children;

        $this->assertCount(0, $children);

        $dirName = 'test';

        $this->mock(StateValidator::class, function(MockInterface $mock): void {
            $mock->shouldReceive('validate')->andReturnNull();
        });

        $this->app->make(CreateDirectoryAction::class)->execute($inode, $dirName);

        $children = $inode->children()->get();
        $file     = $children->first();

        $this->assertCount(1, $children);
        $this->assertSame($inode->id, $file->parent_id);
        $this->assertSame('test', $file->name);
        $this->assertTrue($file->isDirectory());
    }

}