<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * @method static Builder filter(array $filters)
 * @method static Builder sort(string $sort)
 */
trait HasApiFilters
{
    /**
     * Scope a query to filter results.
     *
     * @param  Builder<static>  $query
     * @param  array<string, mixed>|null  $filters
     */
    public function scopeFilter(Builder $query, ?array $filters): void
    {
        if ($filters === null || $filters === []) {
            return;
        }

        foreach ($filters as $key => $value) {
            if ($value === null) {
                continue;
            }

            if ($value === '') {
                continue;
            }

            // Check if model has a dedicated scope for this filter (e.g., scopeName($query, $value))
            $scopeName = 'scope'.Str::studly($key);
            if (method_exists($this, $scopeName)) {
                $query->{Str::camel($key)}($value);

                continue;
            }

            // Default behavior: simple where clause if column exists
            if ($this->isFilterable($key)) {
                $query->where($this->getTable().'.'.$key, $value);
            }
        }
    }

    /**
     * Scope a query to sort results.
     *
     * @param  Builder<static>  $query
     */
    public function scopeSort(Builder $query, ?string $sort): void
    {
        if ($sort === null || $sort === '' || $sort === '0') {
            return;
        }

        $sortFields = explode(',', $sort);

        foreach ($sortFields as $sortField) {
            $direction = 'asc';

            if (str_starts_with($sortField, '-')) {
                $direction = 'desc';
                $sortField = mb_substr($sortField, 1);
            }

            if ($this->isSortable($sortField)) {
                $query->orderBy($this->getTable().'.'.$sortField, $direction);
            }
        }
    }

    protected function isFilterable(string $key): bool
    {
        // If $filterable property exists, check existence
        if (property_exists($this, 'filterable')) {
            /** @var array<int|string, string> $filterable */
            $filterable = $this->filterable;

            return in_array($key, $filterable, true);
        }

        // Default: allow all columns (or safeguard by returning false if safe-by-default desired)
        // For security, it's better to require defining filterable fields.
        return false;
    }

    protected function isSortable(string $key): bool
    {
        // If $sortable property exists, check existence
        if (property_exists($this, 'sortable')) {
            /** @var array<int|string, string> $sortable */
            $sortable = $this->sortable;

            return in_array($key, $sortable, true);
        }

        return false;
    }
}
