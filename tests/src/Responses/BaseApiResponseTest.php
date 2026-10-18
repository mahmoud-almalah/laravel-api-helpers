<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use MahmoudAlmalah\LaravelApiHelpers\Responses\ApiResponse;
use Tests\Stubs\DummyResource;

it('falls back to the configured default messages', function (): void {
    expect(bodyOf(ApiResponse::success())['message'])->toBe('Success')
        ->and(bodyOf(ApiResponse::error(status: 400))['message'])->toBe('An error occurred');
});

it('reads the default messages from config', function (): void {
    config([
        'api-helpers.defaults.message' => 'Done',
        'api-helpers.defaults.error_message' => 'Failed',
    ]);

    expect(bodyOf(ApiResponse::success())['message'])->toBe('Done')
        ->and(bodyOf(ApiResponse::error())['message'])->toBe('Failed');
});

it('ignores non string config values', function (): void {
    config(['api-helpers.defaults.message' => ['not', 'a', 'string']]);

    expect(bodyOf(ApiResponse::success())['message'])->toBe('Success');
});

it('works without a booted container', function (): void {
    $original = Illuminate\Container\Container::getInstance();
    Illuminate\Container\Container::setInstance(new Illuminate\Container\Container());

    try {
        expect(bodyOf(ApiResponse::success())['message'])->toBe('Success');
    } finally {
        Illuminate\Container\Container::setInstance($original);
    }
});

it('keeps the success flag in sync with the status code', function (int $status, bool $success): void {
    expect(bodyOf(ApiResponse::success(status: $status))['success'])->toBe($success);
})->with([
    [200, true],
    [299, true],
    [199, false],
    [300, false],
    [404, false],
]);

it('creates a 201 response', function (): void {
    /** @var Illuminate\Http\JsonResponse $json */
    $json = ApiResponse::created(['id' => 1], 'Created')->toResponse(new Request());

    expect($json->getStatusCode())->toBe(201)
        ->and($json->getData(true))->toBe([
            'success' => true,
            'message' => 'Created',
            'data' => ['id' => 1],
        ]);
});

it('accepts custom resource collections', function (): void {
    $collection = new class([['id' => 1]]) extends ResourceCollection
    {
        public $collects = DummyResource::class;
    };

    expect(bodyOf(ApiResponse::collection('users', $collection)))->toBe([
        'success' => true,
        'message' => 'Success',
        'data' => ['users' => [['id' => 1]]],
        'meta' => null,
    ]);
});

it('keeps the debug payload only when given', function (): void {
    expect(bodyOf(ApiResponse::error('Boom')))->not->toHaveKey('debug')
        ->and(bodyOf(ApiResponse::error('Boom', debug: ['a' => 1]))['debug'])->toBe(['a' => 1]);
});

it('keeps the documented json shape', function (): void {
    expect(array_keys(bodyOf(ApiResponse::success(['a' => 1]))))->toBe(['success', 'message', 'data'])
        ->and(array_keys(bodyOf(ApiResponse::validation(['a' => ['b']]))))->toBe(['success', 'message', 'errors'])
        ->and(array_keys(bodyOf(ApiResponse::model('x', new DummyResource(['id' => 1])))))->toBe(['success', 'message', 'data'])
        ->and(array_keys(bodyOf(ApiResponse::collection('x', DummyResource::collection([])))))->toBe(['success', 'message', 'data', 'meta']);
});
