<?php

namespace App\Validation\StateRules;

use App\Models\Inode;

class InodeIsNotRootRule implements StateRule
{

    ///////////////////////////////////////////////////////////////////////////
    public function __construct(private Inode $inode) {
        //
    }

    ///////////////////////////////////////////////////////////////////////////
    public function isValid(): bool {
        return ( ! $this->inode->isRoot());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function getMessage(): string {
        return __('validation.inode.is_root');
    }

}
