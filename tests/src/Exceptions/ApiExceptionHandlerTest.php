<?php

declare(strict_types=1);

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MahmoudAlmalah\LaravelApiHelpers\Exceptions\ApiExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @return array<string, mixed>
 */
function renderedBody(Throwable $e, ?Request $request = null): array
{
    $response = ApiExceptionHandler::render($e, $request);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    /** @var JsonResponse $response */
    /** @var array<string, mixed> $data */
    $data = $response->getData(true);

    return $data;
}

/**
 * @return array<string, mixed>
 */
function debugOf(Throwable $e, ?Request $request = null): array
{
    $debug = renderedBody($e, $request)['debug'] ?? null;

    expect($debug)->toBeArray();

    /** @var array<string, mixed> $debug */
    return $debug;
}

it('handles validation exceptions', function (): void {
    $validator = Validator::make([], ['name' => 'required']);

    $response = ApiExceptionHandler::render(new ValidationException($validator));

    expect($response->getStatusCode())->toBe(422)
        ->and(renderedBody(new ValidationException($validator)))->toBe([
            'success' => false,
            'message' => 'The name field is required.',
            'errors' => ['name' => ['The name field is required.']],
        ]);
});

it('handles model not found exceptions', function (): void {
    expect(ApiExceptionHandler::render(new ModelNotFoundException())->getStatusCode())->toBe(404)
        ->and(renderedBody(new ModelNotFoundException()))->toMatchArray([
            'success' => false,
            'message' => 'Resource not found',
            'data' => null,
        ]);
});

it('handles model not found exceptions converted to 404 http exceptions by laravel', function (): void {
    $e = new NotFoundHttpException('No query results for model [App\Models\User] 5', new ModelNotFoundException());

    expect(ApiExceptionHandler::render($e)->getStatusCode())->toBe(404)
        ->and(renderedBody($e)['message'])->toBe('Resource not found');
});

it('handles authentication exceptions', function (): void {
    expect(ApiExceptionHandler::render(new AuthenticationException())->getStatusCode())->toBe(401)
        ->and(renderedBody(new AuthenticationException()))->toMatchArray([
            'success' => false,
            'message' => 'Unauthenticated',
            'data' => null,
        ]);
});

it('handles generic exceptions', function (): void {
    expect(ApiExceptionHandler::render(new Exception('Something went wrong', 500))->getStatusCode())->toBe(500)
        ->and(renderedBody(new Exception('Something went wrong', 500)))->toMatchArray([
            'success' => false,
            'message' => 'Something went wrong',
        ]);
});

it('handles authorization exceptions', function (): void {
    expect(ApiExceptionHandler::render(new AuthorizationException('Not yours'))->getStatusCode())->toBe(403)
        ->and(renderedBody(new AuthorizationException('Not yours')))->toMatchArray([
            'success' => false,
            'message' => 'Not yours',
        ])
        ->and(renderedBody(new AuthorizationException())['message'])->toBe('This action is unauthorized.');
});

it('reads exception messages from config', function (): void {
    config([
        'api-helpers.messages.not_found' => 'غير موجود',
        'api-helpers.messages.unauthenticated' => 'غير مصرح',
        'api-helpers.messages.server_error' => 'خطأ في الخادم',
    ]);

    app()['env'] = 'production';

    expect(renderedBody(new ModelNotFoundException())['message'])->toBe('غير موجود')
        ->and(renderedBody(new AuthenticationException())['message'])->toBe('غير مصرح')
        ->and(renderedBody(new Exception('Secret', 500))['message'])->toBe('خطأ في الخادم');
});

describe('register', function (): void {
    beforeEach(function (): void {
        $handler = app(ExceptionHandler::class);

        expect($handler)->toBeInstanceOf(Handler::class);

        /** @var Handler $handler */
        ApiExceptionHandler::register(new Exceptions($handler));
    });

    it('renders api requests with the api format', function (): void {
        $response = app(ExceptionHandler::class)->render(Request::create('/api/users'), new Exception('Boom', 418));

        expect($response)->toBeInstanceOf(JsonResponse::class)
            ->and($response->getStatusCode())->toBe(418)
            ->and(json_decode((string) $response->getContent(), true))->toHaveKeys(['success', 'message', 'data', 'debug']);
    });

    it('renders requests expecting json with the api format', function (): void {
        $request = Request::create('/users', server: ['HTTP_ACCEPT' => 'application/json']);

        $response = app(ExceptionHandler::class)->render($request, new ModelNotFoundException());

        expect($response->getStatusCode())->toBe(404)
            ->and(json_decode((string) $response->getContent(), true))->toMatchArray(['message' => 'Resource not found']);
    });

    it('leaves other requests to laravel', function (): void {
        $response = app(ExceptionHandler::class)->render(Request::create('/users'), new Exception('Boom', 418));

        expect((string) $response->getContent())->not->toContain('"success"');
    });
});

it('uses the exception code as status when it is a valid http status', function (): void {
    expect(ApiExceptionHandler::render(new Exception('Payment Required', 402))->getStatusCode())->toBe(402);
});

it('prefers the status of http exceptions', function (): void {
    expect(ApiExceptionHandler::render(new NotFoundHttpException('Missing'))->getStatusCode())->toBe(404);
});

it('falls back to 500 for codes that are not http statuses', function (int $code): void {
    expect(ApiExceptionHandler::render(new Exception('Boom', $code))->getStatusCode())->toBe(500);
})->with([[0], [99], [600]]);

it('falls back to 500 for non numeric codes such as sql states', function (): void {
    $e = new PDOException('Connection refused');
    $e->errorInfo = ['HY000'];

    (new ReflectionProperty(Exception::class, 'code'))->setValue($e, 'HY000');

    expect(ApiExceptionHandler::render($e)->getStatusCode())->toBe(500);
});

it('falls back to the status text when the exception has no message', function (): void {
    expect(renderedBody(new NotFoundHttpException())['message'])->toBe('Not Found');
});

it('uses the given request instead of the current one', function (): void {
    $request = Request::create('/api/users', 'DELETE');

    expect(data_get(debugOf(new Exception('Boom', 500), $request), 'request'))
        ->toMatchArray(['method' => 'DELETE', 'url' => 'http://localhost/api/users']);
});

describe('in production', function (): void {
    beforeEach(function (): void {
        app()['env'] = 'production';
    });

    it('hides the message of server errors', function (): void {
        expect(renderedBody(new Exception('Sensitive Database Error', 500)))->toBe([
            'success' => false,
            'message' => 'Server Error',
            'data' => null,
        ]);
    });

    it('keeps the message of client errors', function (): void {
        expect(renderedBody(new Exception('Payment Required', 402)))->not->toHaveKey('debug')
            ->and(renderedBody(new Exception('Payment Required', 402))['message'])->toBe('Payment Required');
    });
});

describe('debug payload', function (): void {
    it('describes the exception, request and time', function (): void {
        $request = Request::create('/api/test', 'POST', ['foo' => 'bar']);
        $exception = new Exception('Something went wrong', 500);

        $debug = debugOf($exception, $request);

        expect(data_get($debug, 'exception'))->toMatchArray([
            'class' => Exception::class,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ])
            ->and(data_get($debug, 'request'))->toBe([
                'method' => 'POST',
                'url' => 'http://localhost/api/test',
                'input' => ['foo' => 'bar'],
            ])
            ->and($debug)->toHaveKey('time')
            ->and($debug)->not->toHaveKey('queries');
    });

    it('limits the trace and strips call arguments', function (): void {
        $throw = static function (string $secret, int $depth): never {
            throw new Exception('Deep');
        };
        $nest = static function (int $depth) use (&$nest, $throw): never {
            $depth === 0 ? $throw('top-secret', $depth) : $nest($depth - 1);
        };

        try {
            $nest(10);
        } catch (Exception $exception) {
            $trace = data_get(debugOf($exception, Request::create('/')), 'exception.trace');
        }

        expect($trace)->toBeArray()->toHaveCount(5);

        foreach ((array) $trace as $frame) {
            expect($frame)->not->toHaveKey('args');
        }
    });

    it('never echoes sensitive input', function (): void {
        $request = Request::create('/api/login', 'POST', [
            'email' => 'a@b.c',
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'current_password' => 'secret',
            'token' => 'secret',
        ]);

        expect(data_get(debugOf(new Exception('Boom', 500), $request), 'request.input'))
            ->toBe(['email' => 'a@b.c']);
    });

    it('includes the query log when it is enabled', function (): void {
        DB::connection()->enableQueryLog();
        DB::connection()->select('select 1');

        $debug = debugOf(new Exception('Query Error', 500), Request::create('/'));

        expect(data_get($debug, 'queries'))->toHaveCount(1)
            ->and(data_get($debug, 'queries.0.query'))->toBe('select 1');
    });
});
