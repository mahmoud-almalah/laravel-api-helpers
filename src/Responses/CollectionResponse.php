<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Responses;

use Illuminate\Contracts\Pagination\Paginator as ContractsPaginator;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator as IlluminatePaginator;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

final readonly class CollectionResponse implements Responsable
{
    public function __construct(
        private string $key,
        /** @var array<string, mixed>|AnonymousResourceCollection $collection */
        private array|AnonymousResourceCollection $collection,
        /** @var IlluminatePaginator|ContractsPaginator<string, int>|null $paginator */
        private null|IlluminatePaginator|ContractsPaginator|LengthAwarePaginator $paginator = null,
        #[\Illuminate\Container\Attributes\Config('laravel-api-platform.messages.success')]
        private string $message = 'Success',
        private int $status = Response::HTTP_OK
    ) {}

    public function toResponse($request): JsonResponse
    {
        return new JsonResponse(
            data: [
                'status' => in_array($this->status, Config::array('laravel-api-platform.code.success'), true),
                'message' => $this->message,
                'data' => [
                    $this->key => $this->collection,
                ],
                'meta' => $this->getMeta(),
            ],
            status: $this->status
        );
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
