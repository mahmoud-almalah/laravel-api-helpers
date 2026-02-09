<?php

declare(strict_types=1);

namespace Tests\Stubs;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use MahmoudAlmalah\LaravelApiHelpers\Concerns\HasApiFilters;

final class FilterableModel extends Model
{
    use HasApiFilters;

    protected $table = 'filterable_models';

    protected $guarded = [];

    /** @var array<int, string> */
    protected array $filterable = [
        'name',
        'status',
    ];

    /** @var array<int, string> */
    protected array $sortable = [
        'name',
        'created_at',
    ];

    /**
     * @param  Builder<self>  $query
     */
    public function scopeCustomFilter(Builder $query, string $value): void
    {
        $query->where('custom_column', '=', $value);
    }
}
