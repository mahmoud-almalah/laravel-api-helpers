<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Responses;

use Illuminate\Contracts\Pagination\Paginator as ContractsPaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

final readonly class CollectionResponse extends BaseApiResponse
{
    public function __construct(
        private string $key,
        /** @var array<string, mixed>|AnonymousResourceCollection $collection */
        private array|AnonymousResourceCollection $collection,
        /** @var ContractsPaginator<array-key, mixed>|null $paginator */
        private ?ContractsPaginator $paginator = null,
        ?string $message = null,
        int $status = Response::HTTP_OK
    ) {
        $message ??= ($status >= 200 && $status < 300)
            ? self::resolveConfig('api-helpers.defaults.message', 'Success')
            : self::resolveConfig('api-helpers.defaults.error_message', 'An error occurred');

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
            'meta' => $this->getMeta(),
        ];
    }

    /**
     * @return array<string, bool|int>|null
     */
    private function getMeta(): ?array
    {
        if (is_null($this->paginator)) {
            return null;
        }

        return [
            'current_page' => $this->paginator->currentPage(),
            'per_page' => $this->paginator->perPage(),
            'has_more_pages' => $this->paginator->hasMorePages(),
        ];
    }
}
