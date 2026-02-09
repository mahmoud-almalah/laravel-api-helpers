<?php

declare(strict_types=1);

use MahmoudAlmalah\LaravelApiHelpers\Responses\ModelResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\Stubs\DummyResource;

test('model response returns correct json structure', function (): void {
    $model = (object) ['id' => 1, 'name' => 'Test Model'];

    $resource = new DummyResource($model);

    $response = (new ModelResponse(
        key: 'model',
        resource: $resource,
        message: 'Fetched model',
        status: Response::HTTP_OK
    ))->toResponse(new Illuminate\Http\Request());

    /** @var array{success: bool, message: string, data: array{model: array<string, mixed>}} $responseArray */
    $responseArray = (array) $response->getData(true);

    expect($response->getStatusCode())->toBe(200)
        ->and($responseArray['success'])->toBeTrue()
        ->and($responseArray['message'])->toBe('Fetched model')
        ->and($responseArray['data'])->toBeArray()
        ->and($responseArray['data']['model'])->toMatchArray([
            'id' => 1,
            'name' => 'Test Model',
        ]);
});
