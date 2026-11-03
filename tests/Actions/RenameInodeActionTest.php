<?php

namespace Tests\Actions;

use App\Actions\CreateFileAction;
use App\Actions\RenameInodeAction;
use App\Models\Inode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RenameInodeActionTest extends TestCase
{

    use DatabaseTransactions;

    ///////////////////////////////////////////////////////////////////////////
    public function test_action_executed_successfully(): void {
        $inode = Inode::factory()->directory()->create([
            'name' => 'test',
        ]);

        $inodeName = 'test2';

        $this->assertSame('test', $inode->name);

        $this->app->make(RenameInodeAction::class)->execute($inode, $inodeName);

        $this->assertSame('test2', $inode->name);
    }

}