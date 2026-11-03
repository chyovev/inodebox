<?php

namespace App\Http\Requests;

use App\Models\Inode;
use App\Rules\InodeIsDirectoryRule;
use App\Rules\InodeUniqueNameRule;
use App\Rules\InodeValidNameRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateDirectoryRequest extends FormRequest
{

    ///////////////////////////////////////////////////////////////////////////
    public function rules(): array {
        return [
            'name' => [
                'required',
                'string',
                new InodeIsDirectoryRule($this->getParent()),
                new InodeValidNameRule,
                new InodeUniqueNameRule($this->getParent()),
            ],
        ];
    }

    ///////////////////////////////////////////////////////////////////////////
    public function getDirName(): string {
        return $this->validated('name');
    }

    ///////////////////////////////////////////////////////////////////////////
    private function getParent(): Inode {
        return $this->route('inode');
    }

}
