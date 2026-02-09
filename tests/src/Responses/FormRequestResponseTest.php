<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use MahmoudAlmalah\LaravelApiHelpers\Responses\FormRequestResponse;
use Symfony\Component\HttpFoundation\Response;

test('form request response returns correct validation error structure', function (): void {
    $errors = [
        'email' => ['The email field is required.'],
        'password' => ['The password must be at least 8 characters.'],
    ];

    $response = (new FormRequestResponse(
        message: 'Validation failed',
        data: $errors
    ))->toResponse(new Request());

    $responseArray = (array) $response->getData(true);

    expect($response->getStatusCode())->toBe(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->and($responseArray['success'])->toBeFalse()
        ->and($responseArray['message'])->toBe('Validation failed')
        ->and($responseArray['errors'])->toMatchArray($errors);
});
