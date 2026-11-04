<?php

namespace App\Http\Resources;

use App\Models\Inode;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class InodeContentsResource extends JsonResource
{

    /**
     * Resource property redeclared together with its
     * type in order for intellisense to be reliable
     * instead of using proxy method to dynamically
     * access resource properties and/or methods
     * 
     * @var Inode
     */
    public $resource;

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array {
        return [
            'name'        => $this->resource->name,
            'parents'     => InodeParentChainResource::collection($this->resource->recursiveParents),
            'directories' => InodeContentsInodeResource::collection($this->getDirectories()),
            'files'       => InodeContentsInodeResource::collection($this->getFiles()),
        ];
    }

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @return Collection<Inode>
     */
    private function getDirectories(): Collection {
        return $this
            ->resource
            ->children
            ->filter(function(Inode $inode): bool {
                return $inode->isDirectory();
            })
            ->values(); // reset collection keys to avoid JSON serialization problems
    }

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @return Collection<Inode>
     */
    private function getFiles(): Collection {
        return $this
            ->resource
            ->children
            ->filter(function(Inode $inode): bool {
                return $inode->isFile();
            })
            ->values(); // reset collection keys to avoid JSON serialization problems
    }

}
