<?php

namespace Tests\Http\Requests;

use App\Http\Requests\CreateDirectoryRequest;
use App\Models\Inode;
use App\Rules\InodeIsDirectoryRule;
use App\Rules\InodeUniqueNameRule;
use App\Rules\InodeValidNameRule;
use Mockery\MockInterface;
use Tests\TestCase;

class CreateDirectoryRequestTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_getters(): void {
        $request = $this->partialMock(CreateDirectoryRequest::class, function(MockInterface $mock): void {
            $mock->shouldReceive('route')->andReturn(new Inode);
        });

        $rules = $request->rules();

        $this->assertCount(1, $rules);
        $this->assertCount(5, $rules['name']);

        $this->assertSame('required', $rules['name'][0]);
        $this->assertSame('string', $rules['name'][1]);
        $this->assertSame(InodeIsDirectoryRule::class, get_class($rules['name'][2]));
        $this->assertSame(InodeValidNameRule::class, get_class($rules['name'][3]));
        $this->assertSame(InodeUniqueNameRule::class, get_class($rules['name'][4]));
    }
}