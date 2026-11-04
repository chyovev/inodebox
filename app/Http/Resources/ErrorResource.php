<?php

namespace App\Http\Resources;

use App\Exceptions\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ErrorResource extends JsonResource
{

    /**
     * Resource property redeclared together with its
     * type in order for intellisense to be reliable
     * instead of using proxy method to dynamically
     * access resource properties and/or methods
     * 
     * @var Throwable
     */
    public $resource;

    ///////////////////////////////////////////////////////////////////////////
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array {
        return [
            'error' => $this->getErrorMessage(),
        ];
    }

    ///////////////////////////////////////////////////////////////////////////
    /**
     * Extend the toResponse method in order to disable 'data' wrapping
     * and to add a custom status code to the resource.
     */
    public function toResponse($request): JsonResponse {
        self::withoutWrapping();

        return parent::toResponse($request)->setStatusCode($this->getErrorCode());
    }

    ///////////////////////////////////////////////////////////////////////////
    private function getErrorMessage(): string {
        if ($this->resource instanceof NotFoundHttpException) {
            return __('validation.not_found');
        }

        return $this->resource->getMessage();
    }

    ///////////////////////////////////////////////////////////////////////////
    private function getErrorCode(): int {
        switch (true) {
            case ($this->resource instanceof NotFoundHttpException):
                return Response::HTTP_NOT_FOUND;

            case ($this->resource instanceof ValidationException):
            case ($this->resource instanceof LaravelValidationException):  // thrown by form requests, can be configured to use custom exception
                return Response::HTTP_BAD_REQUEST;

            case ($this->resource instanceof HttpException):
                return $this->resource->getStatusCode();

            default:
                return Response::HTTP_INTERNAL_SERVER_ERROR;
        };
    }

}
