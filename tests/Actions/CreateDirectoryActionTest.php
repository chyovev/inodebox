<?php

namespace Tests\Actions;

use App\Actions\CreateDirectoryAction;
use App\Models\Inode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreateDirectoryActionTest extends TestCase
{

    use DatabaseTransactions;

    ///////////////////////////////////////////////////////////////////////////
    public function test_action_executed_successfully(): void {
        $inode = Inode::factory()->directory()->create();
        $contents = $inode->contents;

        $this->assertCount(0, $contents);

        $dirName = 'test';

        $this->app->make(CreateDirectoryAction::class)->execute($inode, $dirName);

        $contents = $inode->contents()->get();
        $file     = $contents->first();

        $this->assertCount(1, $contents);
        $this->assertSame($inode->id, $file->parent_id);
        $this->assertSame('test', $file->name);
        $this->assertTrue($file->isDirectory());
    }

}