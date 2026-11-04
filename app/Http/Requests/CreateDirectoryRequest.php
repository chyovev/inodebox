<?php

namespace App\Http\Requests;

use App\Validation\InputRules\InodeNameRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateDirectoryRequest extends FormRequest
{

    ///////////////////////////////////////////////////////////////////////////
    public function rules(): array {
        return [
            'name' => ['required', 'string', 'min:1', 'max:255', new InodeNameRule],
        ];
    }

    ///////////////////////////////////////////////////////////////////////////
    public function getDirName(): string {
        return $this->validated('name');
    }

}
