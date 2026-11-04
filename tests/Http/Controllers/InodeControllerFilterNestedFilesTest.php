<?php

namespace Tests\Http\Controllers;

use App\Actions\FilterNestedFilesAction;
use App\Http\Requests\FilterNestedFilesRequest;
use App\Models\Inode;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class InodeControllerFilterNestedFilesTest extends TestCase
{

    use DatabaseTransactions;

    ///////////////////////////////////////////////////////////////////////////
    public function test_inode_not_found(): void {
        $response = $this->get(route('filter_nested_files', ['inode' => 0]));

        $this->assertSame(404, $response->getStatusCode());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_missing_parameters(): void {
        $inode    = Inode::factory()->directory()->create();
        $response = $this->get(route('filter_nested_files', ['inode' => $inode]));

        $this->assertSame(400, $response->getStatusCode());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_nested_files_filtered(): void {
        $this->mock(FilterNestedFilesRequest::class, function(MockInterface $mock): void {
            $mock->shouldReceive('getSearchParam')->once()->andReturn('test');
        });

        $this->mock(FilterNestedFilesAction::class, function(MockInterface $mock): void {
            $mock->shouldReceive('execute')->andReturn(collect([
                Inode::factory()->make([
                    'parent_id' => 4,
                    'name'      => 'test.pdf',
                ])
            ]));
        });

        $inode    = Inode::factory()->directory()->create();
        $response = $this->get(route('filter_nested_files', ['inode' => $inode]));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('[{"parents":[],"parent_id":4,"name":"test.pdf"}]', $response->getContent());
    }

}