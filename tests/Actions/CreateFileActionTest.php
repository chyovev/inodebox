<?php

namespace Tests\Actions;

use App\Actions\CreateFileAction;
use App\Models\Inode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreateFileActionTest extends TestCase
{

    use DatabaseTransactions;

    ///////////////////////////////////////////////////////////////////////////
    public function test_action_executed_successfully(): void {
        $inode = Inode::factory()->directory()->create();
        $contents = $inode->contents;

        $this->assertCount(0, $contents);

        $fileName = 'test.pdf';

        $this->app->make(CreateFileAction::class)->execute($inode, $fileName);

        $contents = $inode->contents()->get();
        $file     = $contents->first();

        $this->assertCount(1, $contents);
        $this->assertSame($inode->id, $file->parent_id);
        $this->assertSame('test.pdf', $file->name);
        $this->assertTrue($file->isFile());
    }

}