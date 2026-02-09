<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Mockery\MockInterface;
use Tests\Stubs\FilterableModel;

it('filters by allowed columns', function (): void {
    /** @var Builder<FilterableModel>&MockInterface $builder */
    $builder = Mockery::mock(Builder::class);

    // Expect where clause for 'status'
    $builder->shouldReceive('where') // @phpstan-ignore-line
        ->once()
        ->with('filterable_models.status', 'active')
        ->andReturnSelf();

    // 'id' is not in $filterable, so it should NOT accept a where for it
    $builder->shouldReceive('where') // @phpstan-ignore-line
        ->never()
        ->with('filterable_models.id', Mockery::any());

    $model = new FilterableModel();
    $model->scopeFilter($builder, ['status' => 'active', 'id' => 1]);
});

it('uses custom scope filters', function (): void {
    /** @var Builder<FilterableModel>&MockInterface $builder */
    $builder = Mockery::mock(Builder::class);

    // The trait should call camelCase version 'customFilter'
    $builder->shouldReceive('customFilter') // @phpstan-ignore-line
        ->once()
        ->with('X')
        ->andReturnSelf();

    $model = new FilterableModel();
    $model->scopeFilter($builder, ['custom_filter' => 'X']);
});

it('sorts by allowed columns asc', function (): void {
    /** @var Builder<FilterableModel>&MockInterface $builder */
    $builder = Mockery::mock(Builder::class);

    $builder->shouldReceive('orderBy') // @phpstan-ignore-line
        ->once()
        ->with('filterable_models.name', 'asc')
        ->andReturnSelf();

    $model = new FilterableModel();
    $model->scopeSort($builder, 'name');
});

it('sorts by allowed columns desc', function (): void {
    /** @var Builder<FilterableModel>&MockInterface $builder */
    $builder = Mockery::mock(Builder::class);

    $builder->shouldReceive('orderBy') // @phpstan-ignore-line
        ->once()
        ->with('filterable_models.name', 'desc')
        ->andReturnSelf();

    $model = new FilterableModel();
    $model->scopeSort($builder, '-name');
});

it('ignores disallowed sort columns', function (): void {
    /** @var Builder<FilterableModel>&MockInterface $builder */
    $builder = Mockery::mock(Builder::class);

    $builder->shouldReceive('orderBy') // @phpstan-ignore-line
        ->never();

    $model = new FilterableModel();
    $model->scopeSort($builder, 'status'); // 'status' is not in $sortable
});
