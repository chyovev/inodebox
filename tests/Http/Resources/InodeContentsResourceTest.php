<?php

namespace Tests\Http\Resources;

use App\Http\Resources\InodeContentsResource;
use App\Models\Inode;
use Tests\TestCase;

class InodeContentsResourceTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_mapping(): void {
        $inode = Inode::factory()->directory()->make(['name' => 'Documents']);
        $inode->setRelation('children', collect([
            Inode::factory()->directory()->make(['id' => 2, 'name' => 'Pdfs']),
            Inode::factory()->file()     ->make(['id' => 3, 'name' => 'important.doc']),
        ]));

        $resource = InodeContentsResource::make($inode);

        $this->assertSame(json_encode([
            'name' => 'Documents',
            'directories' => [
                [
                    'id'   => 2,
                    'name' => 'Pdfs',
                ],
            ],
            'files' => [
                [
                    'id'   => 3,
                    'name' => 'important.doc',
                ]
            ],
        ]), $resource->toJson());
    }

}