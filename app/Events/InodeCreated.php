<?php

namespace App\Events;

use App\Models\Inode;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InodeCreated
{
    use Dispatchable, SerializesModels;

    ///////////////////////////////////////////////////////////////////////////
    public function __construct(readonly public Inode $inode) {
        //
    }

}
