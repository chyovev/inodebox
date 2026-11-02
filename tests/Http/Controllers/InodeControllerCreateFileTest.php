<?php

namespace Tests\Http\Controllers;

use App\Actions\CreateFileAction;
use App\Http\Requests\CreateFileRequest;
use App\Models\Inode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class InodeControllerCreateFileTest extends TestCase
{

    use DatabaseTransactions;

    ///////////////////////////////////////////////////////////////////////////
    public function test_inode_not_found(): void {
        $response = $this->post(route('create_file', ['inode' => 0]));

        $this->assertSame(404, $response->getStatusCode());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_missing_parameters(): void {
        $inode    = Inode::factory()->directory()->create();
        $response = $this->post(route('create_file', ['inode' => $inode]));

        $this->assertSame(422, $response->getStatusCode());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_file_created(): void {
        $this->mock(CreateFileRequest::class, function(MockInterface $mock): void {
            $mock->shouldReceive('getFileName')->once()->andReturn('test.jpg');
        });

        $this->mock(CreateFileAction::class, function(MockInterface $mock): void {
            $mock->shouldReceive('execute')->andReturnNull();
        });

        $inode    = Inode::factory()->directory()->create();
        $response = $this->post(route('create_file', ['inode' => $inode]));

        $this->assertSame(201, $response->getStatusCode());
    }

}