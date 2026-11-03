<?php

namespace App\Validation\StateRules;

use App\Models\Inode;

class InodeIsDirectoryRule implements StateRule
{

    ///////////////////////////////////////////////////////////////////////////
    public function __construct(private Inode $inode) {
        //
    }

    ///////////////////////////////////////////////////////////////////////////
    public function isValid(): bool {
        return $this->inode->isDirectory();
    }

    ///////////////////////////////////////////////////////////////////////////
    public function getMessage(): string {
        return __('validation.inode.not_directory');
    }

}
