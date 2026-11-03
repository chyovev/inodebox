<?php

namespace Tests\Http\Requests;

use App\Http\Requests\RenameInodeRequest;
use App\Validation\InputRules\InodeNameRule;
use Tests\TestCase;

class RenameInodeRequestTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_getters(): void {
        $request = $this->partialMock(RenameInodeRequest::class);

        $rules = $request->rules();

        $this->assertCount(1, $rules);
        $this->assertCount(3, $rules['name']);

        $this->assertSame('required', $rules['name'][0]);
        $this->assertSame('string', $rules['name'][1]);
        $this->assertSame(InodeNameRule::class, get_class($rules['name'][2]));
    }
}