<?php

namespace App\Http\Requests;

use App\Models\Inode;
use App\Rules\InodeIsNotRootRule;
use App\Rules\InodeUniqueNameRule;
use App\Rules\InodeValidNameRule;
use Illuminate\Foundation\Http\FormRequest;

class RenameInodeRequest extends FormRequest
{

    ///////////////////////////////////////////////////////////////////////////
    public function rules(): array {
        return [
            'name' => [
                'required',
                'string',
                new InodeIsNotRootRule($this->getNode()),
                new InodeValidNameRule,
                new InodeUniqueNameRule($this->getParent(), $this->getNode()),
            ],
        ];
    }

    ///////////////////////////////////////////////////////////////////////////
    public function getName(): string {
        return $this->validated('name');
    }

    ///////////////////////////////////////////////////////////////////////////
    /**
     * Only the root inode does not have a parent, and renaming
     * it is not allowed as it's a considered a reserved inode.
     * If that's the case, the root validation rule will fail,
     * but the unique name rule still needs an Inode object,
     * even though it won't get executed.
     */
    private function getParent(): Inode {
        return $this->getNode()->parent ?? new Inode;
    }

    ///////////////////////////////////////////////////////////////////////////
    private function getNode(): Inode {
        return $this->route('inode');
    }

}
