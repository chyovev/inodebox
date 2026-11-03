<?php

namespace Tests\Http\Controllers;

use App\Actions\RenameInodeAction;
use App\Http\Requests\RenameInodeRequest;
use App\Models\Inode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class InodeControllerRenameInodeTest extends TestCase
{

    use DatabaseTransactions;

    ///////////////////////////////////////////////////////////////////////////
    public function test_inode_not_found(): void {
        $response = $this->patch(route('rename_inode', ['inode' => 0]));

        $this->assertSame(404, $response->getStatusCode());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_missing_parameters(): void {
        $parent   = Inode::factory()->directory()->create();
        $inode    = Inode::factory()->for($parent, 'parent')->directory()->create();
        $response = $this->patch(route('rename_inode', ['inode' => $inode]));

        $this->assertSame(422, $response->getStatusCode());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_inode_renamed(): void {
        $this->mock(RenameInodeRequest::class, function(MockInterface $mock): void {
            $mock->shouldReceive('getName')->once()->andReturn('test.jpg');
        });

        $this->mock(RenameInodeAction::class, function(MockInterface $mock): void {
            $mock->shouldReceive('execute')->andReturnNull();
        });

        $inode    = Inode::factory()->directory()->create();
        $response = $this->patch(route('rename_inode', ['inode' => $inode]));

        $this->assertSame(200, $response->getStatusCode());
    }

}