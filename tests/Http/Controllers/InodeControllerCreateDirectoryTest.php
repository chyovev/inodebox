<?php

namespace Tests\Http\Controllers;

use App\Actions\CreateDirectoryAction;
use App\Http\Requests\CreateDirectoryRequest;
use App\Models\Inode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class InodeControllerCreateDirectoryTest extends TestCase
{

    use DatabaseTransactions;

    ///////////////////////////////////////////////////////////////////////////
    public function test_inode_not_found(): void {
        $response = $this->post(route('create_directory', ['inode' => 0]));

        $this->assertSame(404, $response->getStatusCode());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_missing_parameters(): void {
        $inode    = Inode::factory()->directory()->create();
        $response = $this->post(route('create_directory', ['inode' => $inode]));

        $this->assertSame(400, $response->getStatusCode());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_directory_created(): void {
        $this->mock(CreateDirectoryRequest::class, function(MockInterface $mock): void {
            $mock->shouldReceive('getDirName')->once()->andReturn('test');
        });

        $this->mock(CreateDirectoryAction::class, function(MockInterface $mock): void {
            $mock->shouldReceive('execute')->andReturnNull();
        });

        $inode    = Inode::factory()->directory()->create();
        $response = $this->post(route('create_directory', ['inode' => $inode]));

        $this->assertSame(201, $response->getStatusCode());
    }

}