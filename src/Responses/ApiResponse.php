<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Responses;

use Illuminate\Contracts\Pagination\Paginator as ContractsPaginator;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use MahmoudAlmalah\LaravelApiHelpers\Support\ApiConfig;
use Symfony\Component\HttpFoundation\Response;

final class ApiResponse
{
    /**
     * @param  array<string, mixed>|null  $data
     */
    public static function success(?array $data = null, ?string $message = null, int $status = Response::HTTP_OK): Responsable
    {
        return new MessageResponse(
            data: $data,
            message: $message,
            status: $status,
        );
    }

    /**
     * @param  array<string, mixed>|null  $data
     */
    public static function created(?array $data = null, ?string $message = null): Responsable
    {
        return self::success($data, $message, Response::HTTP_CREATED);
    }

    public static function notFound(?string $message = null): Responsable
    {
        return self::error(
            $message ?? ApiConfig::string('api-helpers.messages.not_found', 'Resource not found'),
            Response::HTTP_NOT_FOUND,
        );
    }

    public static function unauthorized(?string $message = null): Responsable
    {
        return self::error(
            $message ?? ApiConfig::string('api-helpers.messages.unauthenticated', 'Unauthenticated'),
            Response::HTTP_UNAUTHORIZED,
        );
    }

    public static function forbidden(?string $message = null): Responsable
    {
        return self::error(
            $message ?? ApiConfig::string('api-helpers.messages.forbidden', 'Forbidden'),
            Response::HTTP_FORBIDDEN,
        );
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @param  array<string, mixed>|null  $debug
     */
    public static function error(
        ?string $message = null,
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
        ?string $message = null,
        int $status = Response::HTTP_OK
    ): Responsable {
        return new ModelResponse(
            key: $key,
            resource: $resource,
            message: $message,
            status: $status,
        );
    }

    /**
     * @param  ContractsPaginator<array-key, mixed>|null  $paginator
     */
    public static function collection(
        string $key,
        ResourceCollection $resource,
        ?string $message = null,
        int $status = Response::HTTP_OK,
        ?ContractsPaginator $paginator = null
    ): Responsable {
        if (! $paginator instanceof ContractsPaginator && ApiConfig::bool('api-helpers.pagination.detect', false)) {
            $paginator = $resource->resource instanceof ContractsPaginator ? $resource->resource : null;
        }

        return new CollectionResponse(
            key: $key,
            collection: $resource,
            paginator: $paginator,
            message: $message,
            status: $status,
        );
    }

    /**
     * @param  array<string, array<int, string>>  $errors
     */
    public static function validation(
        array $errors,
        ?string $message = null
    ): Responsable {
        return new FormRequestResponse(
            data: $errors,
            message: $message,
        );
    }
}
