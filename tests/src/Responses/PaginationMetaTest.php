<?php

declare(strict_types=1);

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use MahmoudAlmalah\LaravelApiHelpers\Responses\ApiResponse;
use Tests\Stubs\DummyResource;

/**
 * @return array<string, mixed>
 */
function paginatedBody(bool $passPaginator): array
{
    $paginator = new LengthAwarePaginator([['id' => 1], ['id' => 2]], total: 7, perPage: 2, currentPage: 1);

    return bodyOf(ApiResponse::collection(
        key: 'items',
        resource: DummyResource::collection($paginator),
        paginator: $passPaginator ? $paginator : null,
    ));
}

it('has no meta without an explicit paginator by default', function (): void {
    expect(paginatedBody(passPaginator: false)['meta'])->toBeNull();
});

it('detects the paginator from the resource when enabled', function (): void {
    config(['api-helpers.pagination.detect' => true]);

    expect(paginatedBody(passPaginator: false)['meta'])->toBe([
        'current_page' => 1,
        'per_page' => 2,
        'has_more_pages' => true,
    ]);
});

it('does not detect a paginator for plain collections', function (): void {
    config(['api-helpers.pagination.detect' => true]);

    expect(bodyOf(ApiResponse::collection('items', DummyResource::collection([['id' => 1]])))['meta'])->toBeNull();
});

it('keeps meta unchanged for length aware paginators by default', function (): void {
    expect(paginatedBody(passPaginator: true)['meta'])->toBe([
        'current_page' => 1,
        'per_page' => 2,
        'has_more_pages' => true,
    ]);
});

it('adds totals to meta when enabled', function (): void {
    config(['api-helpers.pagination.include_totals' => true]);

    expect(paginatedBody(passPaginator: true)['meta'])->toBe([
        'current_page' => 1,
        'per_page' => 2,
        'has_more_pages' => true,
        'total' => 7,
        'last_page' => 4,
    ]);
});

it('skips totals for simple paginators', function (): void {
    config(['api-helpers.pagination.include_totals' => true]);

    $paginator = new Paginator([['id' => 1]], perPage: 1, currentPage: 1);

    expect(bodyOf(ApiResponse::collection('items', DummyResource::collection($paginator), paginator: $paginator))['meta'])
        ->toBe(['current_page' => 1, 'per_page' => 1, 'has_more_pages' => false]);
});
