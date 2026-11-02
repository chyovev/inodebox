<?php

namespace App\Enums;

enum InodeTypeEnum: string
{
    case FILE      = 'F';
    case DIRECTORY = 'D';
}
