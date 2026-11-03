<?php

namespace Tests\Listeners;

use App\Events\InodeCreated;
use App\Listeners\CreateInodeIndex;
use App\Models\Inode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreateInodeIndexTest extends TestCase
{

    use DatabaseTransactions;

    ///////////////////////////////////////////////////////////////////////////
    public function test_single_inode_created(): void {
        $root  = Inode::find(Inode::ROOT_ID);
        $inode = Inode::factory()->for($root, 'parent')->directory()->create();

        $this->assertSame(0, $inode->recursiveParents()->count());

        $event = new InodeCreated($inode);

        $this->app->make(CreateInodeIndex::class)->handle($event);

        $this->assertSame(1, $inode->recursiveParents()->count());
        $this->assertSame($root->id, $inode->recursiveParents->first()->id);
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_nested_inode_created(): void {
        $root      = Inode::find(Inode::ROOT_ID);
        $directory = Inode::factory()->for($root, 'parent')->directory()->create();
        $directory->recursiveParents()->sync([$root->id]);

        $inode = Inode::factory()->for($directory, 'parent')->file()->create();

        $this->assertSame(0, $inode->recursiveParents()->count());

        $event = new InodeCreated($inode);

        $this->app->make(CreateInodeIndex::class)->handle($event);

        $this->assertSame(2, $inode->recursiveParents()->count());
        $this->assertSame($root->id, $inode->recursiveParents->first()->id);
        $this->assertSame($directory->id, $inode->recursiveParents->last()->id);
    }
}