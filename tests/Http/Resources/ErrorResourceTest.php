<?php

namespace Tests\Http\Resources;

use App\Exceptions\ValidationException;
use App\Http\Resources\ErrorResource;
use Exception;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Illuminate\Validation\Validator;
use Mockery\MockInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class ErrorResourceTest extends TestCase
{

    ///////////////////////////////////////////////////////////////////////////
    public function test_not_found_exception(): void {
        $exception = new NotFoundHttpException;
        $resource  = ErrorResource::make($exception);

        $this->assertSame([
            'error' => 'The resource could not be found.',
        ], $resource->resolve(null));
        $this->assertSame(404, $resource->response(null)->getStatusCode());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_custom_validation_exception(): void {
        $exception = new ValidationException('Test');
        $resource  = ErrorResource::make($exception);

        $this->assertSame([
            'error' => 'Test',
        ], $resource->resolve(null));
        $this->assertSame(400, $resource->response(null)->getStatusCode());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_laravel_validation_exception(): void {
        $validator = $this->mock(Validator::class, function(MockInterface $mock): void {
            $mock->shouldReceive('errors')->andReturn(new MessageBag(['Test']));
        });
        $exception = new LaravelValidationException($validator);
        $resource  = ErrorResource::make($exception);

        $this->assertSame([
            'error' => 'Test',
        ], $resource->resolve(null));
        $this->assertSame(400, $resource->response(null)->getStatusCode());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_http_exception(): void {
        $exception = new MethodNotAllowedHttpException(['POST'], 'Test');
        $resource  = ErrorResource::make($exception);

        $this->assertSame([
            'error' => 'Test',
        ], $resource->resolve(null));
        $this->assertSame(405, $resource->response(null)->getStatusCode());
    }

    ///////////////////////////////////////////////////////////////////////////
    public function test_default_exception(): void {
        $exception = new Exception('Test');
        $resource  = ErrorResource::make($exception);

        $this->assertSame([
            'error' => 'Test',
        ], $resource->resolve(null));
        $this->assertSame(500, $resource->response(null)->getStatusCode());
    }

}