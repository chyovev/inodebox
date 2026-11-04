<?php

namespace App\Http\Resources;

use App\Models\Inode;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InodeContentsInodeResource extends JsonResource
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
            'id'   => $this->resource->id,
            'name' => $this->resource->name,
        ];
    }

}
