<?php

namespace Tests\Http\Requests;

use App\Http\Requests\CreateFileRequest;
use App\Validation\InputRules\InodeNameRule;
use Tests\TestCase;

class CreateFileRequestTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_getters(): void {
        $request = $this->partialMock(CreateFileRequest::class);

        $rules = $request->rules();

        $this->assertCount(1, $rules);
        $this->assertCount(5, $rules['name']);

        $this->assertSame('required', $rules['name'][0]);
        $this->assertSame('string', $rules['name'][1]);
        $this->assertSame('min:1', $rules['name'][2]);
        $this->assertSame('max:255', $rules['name'][3]);
        $this->assertSame(InodeNameRule::class, get_class($rules['name'][4]));
    }
}