<?php

namespace Tests\Http\Controllers;

use App\Actions\DeleteInodeAction;
use App\Models\Inode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class InodeControllerDeleteInodeTest extends TestCase
{

    use DatabaseTransactions;

    ///////////////////////////////////////////////////////////////////////////
    public function test_inode_not_found(): void {
        $response = $this->delete(route('delete_inode', ['inode' => 0]));

        $this->assertSame(404, $response->getStatusCode());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_inode_deleted(): void {
        $this->mock(DeleteInodeAction::class, function(MockInterface $mock): void {
            $mock->shouldReceive('execute')->andReturnNull();
        });

        $inode    = Inode::factory()->directory()->create();
        $response = $this->delete(route('delete_inode', ['inode' => $inode]));

        $this->assertSame(204, $response->getStatusCode());
    }

}