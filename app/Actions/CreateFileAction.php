<?php

namespace App\Actions;

use App\Enums\InodeTypeEnum;
use App\Models\Inode;

class CreateFileAction
{

    ///////////////////////////////////////////////////////////////////////////
    public function execute(Inode $parent, string $fileName): void {
        $parent->contents()->create([
            'name' => $fileName,
            'type' => InodeTypeEnum::FILE,
        ]);
    }

}
