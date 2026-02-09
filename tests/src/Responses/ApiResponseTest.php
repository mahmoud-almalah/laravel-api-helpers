<?php

declare(strict_types=1);

use MahmoudAlmalah\LaravelApiHelpers\Responses\ApiResponse;
use MahmoudAlmalah\LaravelApiHelpers\Responses\CollectionResponse;
use MahmoudAlmalah\LaravelApiHelpers\Responses\MessageResponse;
use MahmoudAlmalah\LaravelApiHelpers\Responses\ModelResponse;
use Tests\Stubs\DummyResource;

it('creates a success response', function (): void {
    $response = ApiResponse::success(['foo' => 'bar']);

    expect($response)->toBeInstanceOf(MessageResponse::class);
});

it('creates an error response', function (): void {
    $response = ApiResponse::error('Something went wrong');

    expect($response)->toBeInstanceOf(MessageResponse::class);
});

it('creates a model response', function (): void {
    $resource = new DummyResource(['id' => 1]);
    $response = ApiResponse::model('user', $resource);

    expect($response)->toBeInstanceOf(ModelResponse::class);
});

it('creates a collection response', function (): void {
    $resource = DummyResource::collection([['id' => 1]]);
    $response = ApiResponse::collection('users', $resource);

    expect($response)->toBeInstanceOf(CollectionResponse::class);
});
