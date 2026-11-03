<?php

namespace Tests\Http\Requests;

use App\Http\Requests\FilterNestedFilesRequest;
use Tests\TestCase;

class FilterNestedFilesRequestTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_getters(): void {
        $request = $this->partialMock(FilterNestedFilesRequest::class);

        $rules = $request->rules();

        $this->assertCount(1, $rules);
        $this->assertCount(3, $rules['search']);

        $this->assertSame('required', $rules['search'][0]);
        $this->assertSame('string', $rules['search'][1]);
        $this->assertSame('min:3', $rules['search'][2]);
    }
}