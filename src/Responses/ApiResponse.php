<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

final class ApiResponse
{
    /**
     * @param  array<string, mixed>|null  $data
     */
    public static function success(?array $data = null, string $message = 'Success', int $status = Response::HTTP_OK): Responsable
    {
        return new MessageResponse(
            data: $data,
            message: $message,
            status: $status,
        );
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @param  array<string, mixed>|null  $debug
     */
    public static function error(
        string $message = 'Error',
        int $status = Response::HTTP_INTERNAL_SERVER_ERROR,
        ?array $data = null,
        ?array $debug = null
    ): Responsable {
        return new MessageResponse(
            data: $data,
            message: $message,
            status: $status,
            debug: $debug,
        );
    }

    public static function model(
        string $key,
        JsonResource $resource,
        string $message = 'Success',
        int $status = Response::HTTP_OK
    ): Responsable {
        return new ModelResponse(
            key: $key,
            resource: $resource,
            message: $message,
            status: $status,
        );
    }

    public static function collection(
        string $key,
        AnonymousResourceCollection $resource,
        string $message = 'Success',
        int $status = Response::HTTP_OK
    ): Responsable {
        return new CollectionResponse(
            key: $key,
            collection: $resource,
            message: $message,
            status: $status,
        );
    }

    /**
     * @param  array<string, array<string>>  $errors
     */
    public static function validation(
        array $errors,
        string $message = 'Validation failed'
    ): Responsable {
        return new FormRequestResponse(
            data: $errors,
            message: $message,
        );
    }
}
