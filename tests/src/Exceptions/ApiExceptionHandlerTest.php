<?php

declare(strict_types=1);

use Illuminate\Auth\AuthenticationException;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use MahmoudAlmalah\LaravelApiHelpers\Exceptions\ApiExceptionHandler;

it('handles validation exceptions', function (): void {
    /** @var Validator&Mockery\MockInterface $validator */
    $validator = Mockery::mock(Validator::class);
    $validator->shouldReceive('errors->messages')->andReturn(['field' => ['Error']]); // @phpstan-ignore-line
    $validator->shouldReceive('errors->all')->andReturn(['Error']); // @phpstan-ignore-line

    $exception = new ValidationException($validator);

    $request = Illuminate\Http\Request::create('/', 'GET');
    app()->instance('request', $request);

    $response = ApiExceptionHandler::render($exception);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    /** @var JsonResponse $response */
    expect($response->getStatusCode())->toBe(422)
        ->and($response->getData(true))->toMatchArray([
            'success' => false,
            // 'message' => 'The given data was invalid.', // Dependent on Laravel version/mocking
            'message' => 'Error', // Since we mocked errors->all() returning ['Error'], ValidationException sets message to 'Error'
            'errors' => ['field' => ['Error']],
        ]);
});

it('handles model not found exceptions', function (): void {
    $request = Illuminate\Http\Request::create('/', 'GET');
    app()->instance('request', $request);

    $exception = new ModelNotFoundException();

    $response = ApiExceptionHandler::render($exception);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    /** @var JsonResponse $response */
    expect($response->getStatusCode())->toBe(404)
        ->and($response->getData(true))->toMatchArray([
            'success' => false,
            'message' => 'Resource not found',
        ]);
});

it('handles authentication exceptions', function (): void {
    $request = Illuminate\Http\Request::create('/', 'GET');
    app()->instance('request', $request);

    $exception = new AuthenticationException();

    $response = ApiExceptionHandler::render($exception);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    /** @var JsonResponse $response */
    expect($response->getStatusCode())->toBe(401)
        ->and($response->getData(true))->toMatchArray([
            'success' => false,
            'message' => 'Unauthenticated',
        ]);
});

it('handles generic exceptions', function (): void {
    $request = Illuminate\Http\Request::create('/', 'GET');
    app()->instance('request', $request);

    $exception = new Exception('Something went wrong', 500);

    $response = ApiExceptionHandler::render($exception);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    /** @var JsonResponse $response */
    expect($response->getStatusCode())->toBe(500)
        ->and($response->getData(true))->toMatchArray([
            'success' => false,
            'message' => 'Something went wrong',
        ]);
});

it('handles exceptions with custom status codes', function (): void {
    $request = Illuminate\Http\Request::create('/', 'GET');
    app()->instance('request', $request);

    $exception = new Exception('Payment Required', 402);

    $response = ApiExceptionHandler::render($exception);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    /** @var JsonResponse $response */
    expect($response->getStatusCode())->toBe(402)
        ->and($response->getData(true))->toMatchArray([
            'success' => false,
            'message' => 'Payment Required',
        ]);
});

it('hides error message in production', function (): void {
    /** @var Application&Mockery\MockInterface $app */
    $app = Mockery::mock(Application::class);
    $app->shouldReceive('environment')->with('production')->andReturn(true); // @phpstan-ignore-line

    $request = Illuminate\Http\Request::create('/', 'GET');
    $app->shouldReceive('make')->with('request', [])->andReturn($request); // @phpstan-ignore-line
    $app->shouldReceive('bound')->with('request')->andReturn(true); // @phpstan-ignore-line
    $app->shouldReceive('offsetGet')->with('request')->andReturn($request); // @phpstan-ignore-line

    // Swap container
    $original = Container::getInstance();
    Container::setInstance($app);

    $exception = new Exception('Sensitive Database Error', 500);

    $response = ApiExceptionHandler::render($exception);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    /** @var JsonResponse $response */
    /** @var array<string, mixed> $data */
    $data = $response->getData(true);
    expect($data['message'])->toBe('Server Error');

    // Restore
    Container::setInstance($original);
});
