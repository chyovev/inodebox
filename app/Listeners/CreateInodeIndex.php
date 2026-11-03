<?php

namespace App\Listeners;

use App\Events\InodeCreated;
use App\Models\Inode;

class CreateInodeIndex
{

    ///////////////////////////////////////////////////////////////////////////
    public function handle(InodeCreated $event): void {
        $inode = $event->inode;
        $ids   = $this->getAllParentsIds($inode);
        
        $inode->recursiveParents()->sync($ids);
    }

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @return int[]
     */
    private function getAllParentsIds(Inode $inode): array {
        $parent  = $inode->parent;
        $parents = $parent->recursiveParents;

        return $parents->pluck('id')->prepend($parent->id)->toArray();
    }

}
