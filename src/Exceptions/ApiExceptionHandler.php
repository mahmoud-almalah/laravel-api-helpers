<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use MahmoudAlmalah\LaravelApiHelpers\Responses\ApiResponse;
use MahmoudAlmalah\LaravelApiHelpers\Support\ApiConfig;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

final class ApiExceptionHandler
{
    /**
     * Request input that is never echoed back in the debug payload.
     *
     * @var list<string>
     */
    private const array HIDDEN_INPUT = ['password', 'password_confirmation', 'current_password', 'token'];

    private const int TRACE_LIMIT = 5;

    /**
     * Render an exception into an HTTP response.
     */
    public static function render(Throwable $e, ?Request $request = null): Response
    {
        $request ??= request();

        return self::toApiResponse($e, $request)->toResponse($request);
    }

    /**
     * Register the handler for API requests (`api/*` by default) and any
     * request that expects JSON. Other requests keep Laravel's rendering.
     *
     * @param  list<string>  $paths
     */
    public static function register(Exceptions $exceptions, array $paths = ['api/*']): void
    {
        $exceptions->render(static fn (Throwable $e, Request $request): ?Response => $request->is($paths) || $request->expectsJson()
            ? self::render($e, $request)
            : null);
    }

    private static function toApiResponse(Throwable $e, Request $request): Responsable
    {
        return match (true) {
            $e instanceof ValidationException => ApiResponse::validation($e->errors(), $e->getMessage()),
            self::isModelNotFound($e) => ApiResponse::notFound(),
            $e instanceof AuthenticationException => ApiResponse::unauthorized(),
            $e instanceof AuthorizationException => ApiResponse::forbidden($e->getMessage() ?: null),
            default => self::toGenericResponse($e, $request),
        };
    }

    /**
     * Laravel converts a ModelNotFoundException into a NotFoundHttpException
     * before custom renderers run, so the original is found on the previous chain.
     */
    private static function isModelNotFound(Throwable $e): bool
    {
        return $e instanceof ModelNotFoundException
            || ($e instanceof NotFoundHttpException && $e->getPrevious() instanceof ModelNotFoundException);
    }

    private static function toGenericResponse(Throwable $e, Request $request): Responsable
    {
        $status = self::resolveStatusCode($e);
        $isProduction = app()->environment('production');

        $message = $isProduction && $status === Response::HTTP_INTERNAL_SERVER_ERROR
            ? ApiConfig::message('server_error')
            : $e->getMessage();

        return ApiResponse::error(
            message: $message === '' ? (Response::$statusTexts[$status] ?? 'Error') : $message,
            status: $status,
            debug: $isProduction ? null : self::buildDebugPayload($e, $request),
        );
    }

    private static function resolveStatusCode(Throwable $e): int
    {
        $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : $e->getCode();

        // Some exceptions (e.g. PDOException) carry non-numeric or out-of-range codes.
        if (! is_numeric($status) || $status < 100 || $status > 599) {
            return Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return (int) $status;
    }

    /**
     * @return array<string, mixed>
     */
    private static function buildDebugPayload(Throwable $e, Request $request): array
    {
        $debug = [
            'exception' => [
                'class' => $e::class,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => self::buildTrace($e),
            ],
            'request' => [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'input' => $request->except(self::HIDDEN_INPUT),
            ],
            'time' => now()->toIso8601String(),
        ];

        $queries = self::queryLog();

        if ($queries !== []) {
            $debug['queries'] = $queries;
        }

        return $debug;
    }

    /**
     * The first frames of the trace, without call arguments
     * (they can hold secrets and are often not JSON-encodable).
     *
     * @return list<array<string, mixed>>
     */
    private static function buildTrace(Throwable $e): array
    {
        return array_map(
            static fn (array $frame): array => Arr::except($frame, 'args'),
            array_slice($e->getTrace(), 0, self::TRACE_LIMIT),
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function queryLog(): array
    {
        try {
            return DB::connection()->logging() ? DB::getQueryLog() : [];
        } catch (Throwable) {
            return [];
        }
    }
}
