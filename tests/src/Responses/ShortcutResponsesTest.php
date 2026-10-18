<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use MahmoudAlmalah\LaravelApiHelpers\Responses\ApiResponse;

it('creates status shortcut responses', function (string $method, int $status, string $message): void {
    /** @var Illuminate\Http\JsonResponse $json */
    $json = ApiResponse::$method()->toResponse(new Request());

    expect($json->getStatusCode())->toBe($status)
        ->and($json->getData(true))->toBe(['success' => false, 'message' => $message, 'data' => null]);
})->with([
    ['notFound', 404, 'Resource not found'],
    ['unauthorized', 401, 'Unauthenticated'],
    ['forbidden', 403, 'Forbidden'],
]);

it('accepts a custom message in shortcut responses', function (): void {
    expect(bodyOf(ApiResponse::notFound('No such user'))['message'])->toBe('No such user');
});

it('keeps an explicit validation message', function (): void {
    expect(bodyOf(ApiResponse::validation(['a' => ['b']], 'Custom'))['message'])->toBe('Custom');
});
