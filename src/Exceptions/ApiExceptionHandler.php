<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use MahmoudAlmalah\LaravelApiHelpers\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ApiExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     */
    public static function render(Throwable $e): Response
    {
        return match (true) {
            $e instanceof ValidationException => self::renderValidation($e),
            $e instanceof ModelNotFoundException => self::renderModelNotFound(),
            $e instanceof AuthenticationException => self::renderAuthentication(),
            default => self::renderGeneric($e),
        };
    }

    private static function renderValidation(ValidationException $e): Response
    {
        return ApiResponse::validation($e->errors(), $e->getMessage())->toResponse(request());
    }

    private static function renderModelNotFound(): Response
    {
        return ApiResponse::error(
            message: 'Resource not found',
            status: Response::HTTP_NOT_FOUND
        )->toResponse(request());
    }

    private static function renderAuthentication(): Response
    {
        return ApiResponse::error(
            message: 'Unauthenticated',
            status: Response::HTTP_UNAUTHORIZED
        )->toResponse(request());
    }

    private static function renderGeneric(Throwable $e): Response
    {
        $statusCode = self::resolveStatusCode($e);
        $message = $e->getMessage();

        // Hide internal error details in production generally, but here we just pass message.
        if (app()->environment('production') && $statusCode === Response::HTTP_INTERNAL_SERVER_ERROR) {
            $message = 'Server Error';
        }

        return ApiResponse::error(
            message: $message,
            status: $statusCode,
            debug: self::buildDebugPayload($e)
        )->toResponse(request());
    }

    private static function resolveStatusCode(Throwable $e): int
    {
        // If specific HTTP interface is used
        if (method_exists($e, 'getStatusCode')) {
            return (int) $e->getStatusCode();
        }

        $statusCode = $e->getCode();

        // Ensure status code is a valid HTTP status (100-599)
        // PDOExceptions often have alphanumeric codes
        if (! is_int($statusCode) || $statusCode < 100 || $statusCode > 599) {
            return Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return $statusCode;
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function buildDebugPayload(Throwable $e): ?array
    {
        if (app()->environment('production')) {
            return null;
        }

        $currentRequest = request();

        $debug = [
            'exception' => [
                'class' => $e::class,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->take(5)->toArray(), // Limit trace
            ],
            'request' => [
                'method' => $currentRequest->method(),
                'url' => $currentRequest->fullUrl(),
                'input' => $currentRequest->all(),
            ],
            'time' => now()->toIso8601String(),
        ];

        // Attempt to get query logs if enabled
        if (method_exists(DB::class, 'getQueryLog')) {
            $queries = DB::getQueryLog();
            if (! empty($queries)) {
                $debug['queries'] = $queries;
            }
        }

        return $debug;
    }
}
