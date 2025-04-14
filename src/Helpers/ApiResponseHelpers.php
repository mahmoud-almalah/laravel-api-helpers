<?php

declare(strict_types=1);

use Illuminate\Contracts\Support\Responsable;

if (! function_exists('api_success')) {
    /**
     * @param  array<string, mixed>|null  $data
     */
    function api_success(?array $data = null, string $message = 'Success', int $status = 200): Responsable
    {
        return new MahmoudAlmalah\LaravelApiHelpers\Responses\MessageResponse(
            data: $data,
            message: $message,
            status: $status,
        );
    }
}

if (! function_exists('api_error')) {
    /**
     * @param  array<string, mixed>|null  $data
     */
    function api_error(?array $data = null, string $message = 'Error', int $status = 500): Responsable
    {
        return new MahmoudAlmalah\LaravelApiHelpers\Responses\MessageResponse(
            data: $data,
            message: $message,
            status: $status,
        );
    }
}

if (! function_exists('api_validation_error')) {
    /**
     * @param  array<string, array<int, string>>  $errors
     */
    function api_validation_error(array $errors, string $message = 'Validation Error'): Responsable
    {
        return new MahmoudAlmalah\LaravelApiHelpers\Responses\FormRequestResponse(
            message: $message,
            data: $errors,
        );
    }
}

if (! function_exists('api_model_response')) {
    function api_model_response(
        string $key,
        Illuminate\Http\Resources\Json\JsonResource $resource,
        string $message = 'Success',
        int $status = 200
    ): Responsable {
        return new MahmoudAlmalah\LaravelApiHelpers\Responses\ModelResponse(
            key: $key,
            resource: $resource,
            message: $message,
            status: $status,
        );
    }
}

if (! function_exists('api_collection_response')) {
    function api_collection_response(
        string $key,
        Illuminate\Http\Resources\Json\AnonymousResourceCollection $resource,
        string $message = 'Success',
        int $status = 200
    ): Responsable {
        return new MahmoudAlmalah\LaravelApiHelpers\Responses\CollectionResponse(
            key: $key,
            collection: $resource,
            message: $message,
            status: $status,
        );
    }
}
