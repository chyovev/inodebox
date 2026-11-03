<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterNestedFilesRequest extends FormRequest
{

    ///////////////////////////////////////////////////////////////////////////
    public function rules(): array {
        return [
            'search' => ['required', 'string', 'min:3'],
        ];
    }

    ///////////////////////////////////////////////////////////////////////////
    public function getSearchParam(): string {
        return $this->validated('search');
    }

}
