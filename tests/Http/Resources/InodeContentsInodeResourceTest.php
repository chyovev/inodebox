<?php

namespace Tests\Http\Resources;

use App\Http\Resources\InodeContentsInodeResource;
use App\Models\Inode;
use Tests\TestCase;

class InodeContentsInodeResourceTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_mapping(): void {
        $inode = Inode::factory()->make([
            'id'   => 4,
            'name' => 'test.pdf',
        ]);

        $resource = InodeContentsInodeResource::make($inode);

        $this->assertSame([
            'id'   => 4,
            'name' => 'test.pdf',
        ], $resource->resolve(null));
    }

}