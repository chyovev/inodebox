<?php

namespace Tests\Http\Resources;

use App\Http\Resources\InodeParentChainResource;
use App\Models\Inode;
use Tests\TestCase;

class InodeParentChainResourceTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_mapping(): void {
        $inode = Inode::factory()->make([
            'id'   => 4,
            'name' => 'test',
        ]);

        $resource = InodeParentChainResource::make($inode);

        $this->assertSame([
            'id'   => 4,
            'name' => 'test',
        ], $resource->resolve(null));
    }

}