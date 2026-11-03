<?php

namespace App\Actions;

use App\Models\Inode;

class RenameInodeAction
{

    ///////////////////////////////////////////////////////////////////////////
    public function execute(Inode $inode, string $inodeName): void {
        $inode->update([
            'name' => $inodeName,
        ]);
    }

}
