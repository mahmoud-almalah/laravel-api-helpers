<?php

declare(strict_types=1);

use MahmoudAlmalah\LaravelApiHelpers\Responses\MessageResponse;
use Symfony\Component\HttpFoundation\Response;

test('message response returns correct json structure', function (): void {
    $data = ['key' => 'value'];

    $response = (new MessageResponse(
        data: $data,
        message: 'Operation successful',
        status: Response::HTTP_OK
    ))->toResponse(new Illuminate\Http\Request());

    /** @var array{success: bool, message: string, data: array<string, mixed>} $responseArray */
    $responseArray = (array) $response->getData(true);

    expect($response->getStatusCode())->toBe(200)
        ->and($responseArray['success'])->toBeTrue()
        ->and($responseArray['message'])->toBe('Operation successful')
        ->and($responseArray['data'])->toEqual($data);
});

test('message response returns correct json structure with null data', function (): void {
    $response = (new MessageResponse(
        message: 'Operation successful',
        status: Response::HTTP_OK
    ))->toResponse(new Illuminate\Http\Request());

    /** @var array{success: bool, message: string, data: null} $responseArray */
    $responseArray = (array) $response->getData(true);

    expect($response->getStatusCode())->toBe(200)
        ->and($responseArray['success'])->toBeTrue()
        ->and($responseArray['message'])->toBe('Operation successful')
        ->and($responseArray['data'])->toBeNull();
});

test('message response returns correct json structure with custom status code', function (): void {
    $response = (new MessageResponse(
        message: 'Operation successful',
        status: Response::HTTP_CREATED
    ))->toResponse(new Illuminate\Http\Request());

    /** @var array{success: bool, message: string, data: null} $responseArray */
    $responseArray = (array) $response->getData(true);

    expect($response->getStatusCode())->toBe(201)
        ->and($responseArray['success'])->toBeTrue()
        ->and($responseArray['message'])->toBe('Operation successful')
        ->and($responseArray['data'])->toBeNull();
});

test('message response returns correct json structure with error status', function (): void {
    $response = (new MessageResponse(
        message: 'Operation failed',
        status: Response::HTTP_BAD_REQUEST
    ))->toResponse(new Illuminate\Http\Request());

    /** @var array{success: bool, message: string, data: null} $responseArray */
    $responseArray = (array) $response->getData(true);

    expect($response->getStatusCode())->toBe(400)
        ->and($responseArray['success'])->toBeFalse()
        ->and($responseArray['message'])->toBe('Operation failed')
        ->and($responseArray['data'])->toBeNull();
});

test('message response returns correct json structure with custom message and status', function (): void {
    $response = (new MessageResponse(
        message: 'Custom error message',
        status: Response::HTTP_UNAUTHORIZED
    ))->toResponse(new Illuminate\Http\Request());

    /** @var array{success: bool, message: string, data: null} $responseArray */
    $responseArray = (array) $response->getData(true);

    expect($response->getStatusCode())->toBe(401)
        ->and($responseArray['success'])->toBeFalse()
        ->and($responseArray['message'])->toBe('Custom error message')
        ->and($responseArray['data'])->toBeNull();
});
