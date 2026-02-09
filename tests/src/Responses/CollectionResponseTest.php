<?php

declare(strict_types=1);

use MahmoudAlmalah\LaravelApiHelpers\Responses\CollectionResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\Stubs\DummyResource;

test('collection response returns correct json structure without pagination', function (): void {
    $items = collect([
        (object) ['id' => 1, 'name' => 'Item 1'],
        (object) ['id' => 2, 'name' => 'Item 2'],
    ]);

    $resource = DummyResource::collection($items);

    $response = (new CollectionResponse(
        key: 'items',
        collection: $resource,
        paginator: null,
        message: 'Fetched successfully',
        status: Response::HTTP_OK
    ))->toResponse(new Illuminate\Http\Request());

    /** @var array{success: bool, message: string, data: array{items: array<int, mixed>}, meta: null} $responseArray */
    $responseArray = (array) $response->getData(true);

    expect($response->getStatusCode())->toBe(200)
        ->and($responseArray['success'])->toBeTrue()
        ->and($responseArray['message'])->toBe('Fetched successfully')
        ->and($responseArray['data'])->toBeArray()
        ->and($responseArray['data']['items'])->toHaveCount(2)
        ->and($responseArray['meta'])->toBeNull();
});

test('collection response returns correct json structure with pagination', function (): void {
    $items = collect([
        (object) ['id' => 1, 'name' => 'Item 1'],
        (object) ['id' => 2, 'name' => 'Item 2'],
    ]);

    $paginator = new Illuminate\Pagination\Paginator(
        items: $items,
        perPage: 2,
        currentPage: 1,
        options: ['path' => '/']
    );

    $resource = DummyResource::collection($paginator);

    $response = (new CollectionResponse(
        key: 'items',
        collection: $resource,
        paginator: $paginator, // @phpstan-ignore-line
        message: 'Paginated fetch',
        status: Response::HTTP_OK
    ))->toResponse(new Illuminate\Http\Request());

    /** @var array{success: bool, message: string, data: array{items: array<int, mixed>}, meta: array<string, mixed>} $responseArray */
    $responseArray = (array) $response->getData(true);

    expect($response->getStatusCode())->toBe(200)
        ->and($responseArray['success'])->toBeTrue()
        ->and($responseArray['message'])->toBe('Paginated fetch')
        ->and($responseArray['data'])->toBeArray()
        ->and($responseArray['data']['items'])->toHaveCount(2)
        ->and($responseArray['meta'])->toMatchArray([
            'current_page' => 1,
            'per_page' => 2,
            'has_more_pages' => false,
        ]);
});

test('collection response returns correct json structure with empty collection', function (): void {
    $items = collect([]);

    $paginator = new Illuminate\Pagination\Paginator(
        items: $items,
        perPage: 2,
        currentPage: 1,
        options: ['path' => '/']
    );

    $resource = DummyResource::collection($paginator);

    $response = (new CollectionResponse(
        key: 'items',
        collection: $resource,
        paginator: $paginator,
        message: 'No items found',
        status: Response::HTTP_OK
    ))->toResponse(new Illuminate\Http\Request());

    /** @var array{success: bool, message: string, data: array{items: array<int, mixed>}, meta: array<string, mixed>} $responseArray */
    $responseArray = (array) $response->getData(true);

    expect($response->getStatusCode())->toBe(200)
        ->and($responseArray['success'])->toBeTrue()
        ->and($responseArray['message'])->toBe('No items found')
        ->and($responseArray['data'])->toBeArray()
        ->and($responseArray['data']['items'])->toHaveCount(0)
        ->and($responseArray['meta'])->toMatchArray([
            'current_page' => 1,
            'per_page' => 2,
            'has_more_pages' => false,
        ]);
});
