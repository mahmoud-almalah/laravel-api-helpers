<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Responses;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator as ContractsPaginator;
use Illuminate\Http\Resources\Json\ResourceCollection;
use MahmoudAlmalah\LaravelApiHelpers\Support\ApiConfig;
use Symfony\Component\HttpFoundation\Response;

final readonly class CollectionResponse extends BaseApiResponse
{
    /**
     * @param  array<array-key, mixed>|ResourceCollection  $collection
     * @param  ContractsPaginator<array-key, mixed>|null  $paginator
     */
    public function __construct(
        private string $key,
        private array|ResourceCollection $collection,
        private ?ContractsPaginator $paginator = null,
        ?string $message = null,
        int $status = Response::HTTP_OK,
    ) {
        parent::__construct($message, $status);
    }

    protected function buildPayload(): array
    {
        return [
            'success' => $this->isSuccessful(),
            'message' => $this->message,
            'data' => [
                $this->key => $this->collection,
            ],
            'meta' => $this->buildMeta(),
        ];
    }

    /**
     * @return array<string, bool|int>|null
     */
    private function buildMeta(): ?array
    {
        if (! $this->paginator instanceof ContractsPaginator) {
            return null;
        }

        $meta = [
            'current_page' => $this->paginator->currentPage(),
            'per_page' => $this->paginator->perPage(),
            'has_more_pages' => $this->paginator->hasMorePages(),
        ];

        if ($this->paginator instanceof LengthAwarePaginator && ApiConfig::bool('api-helpers.pagination.include_totals', false)) {
            $meta['total'] = $this->paginator->total();
            $meta['last_page'] = $this->paginator->lastPage();
        }

        return $meta;
    }
}
