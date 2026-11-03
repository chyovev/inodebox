<?php

namespace App\Http\Requests;

use App\Validation\InputRules\InodeNameRule;
use Illuminate\Foundation\Http\FormRequest;

class RenameInodeRequest extends FormRequest
{

    ///////////////////////////////////////////////////////////////////////////
    public function rules(): array {
        return [
            'name' => ['required', 'string', new InodeNameRule],
        ];
    }

    ///////////////////////////////////////////////////////////////////////////
    public function getName(): string {
        return $this->validated('name');
    }

}
