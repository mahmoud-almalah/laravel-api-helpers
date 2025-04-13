<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use MahmoudAlmalah\LaravelApiHelpers\Responses\FormRequestResponse;
use Symfony\Component\HttpFoundation\Response;

test('form request response returns correct validation error structure', function (): void {
    $errors = [
        'email' => ['The email field is required.'],
        'password' => ['The password must be at least 8 characters.'],
    ];

    $response = (new FormRequestResponse($errors))->toResponse(new Request());

    /** @var array{
     *     status: bool,
     *     message: string,
     *     data: array<string, array<int, string>>
     * } $responseArray */
    $responseArray = $response->getData(true);

    expect($response->getStatusCode())->toBe(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->and($responseArray['status'])->toBeFalse()
        ->and($responseArray['message'])->toBe(Config::string('http-status.messages.validation'))
        ->and($responseArray['data'])->toMatchArray($errors);
});
