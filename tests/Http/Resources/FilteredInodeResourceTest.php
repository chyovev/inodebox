<?php

namespace Tests\Http\Resources;

use App\Http\Resources\FilteredInodeResource;
use App\Models\Inode;
use Tests\TestCase;

class FilteredInodeResourceTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_mapping(): void {
        $inode = Inode::factory()->make([
            'parent_id' => 4,
            'name'      => 'test.pdf',
        ]);

        $resource = FilteredInodeResource::make($inode);

        $this->assertSame([
            'parent_id' => 4,
            'name'      => 'test.pdf',
        ], $resource->resolve(null));
    }

}