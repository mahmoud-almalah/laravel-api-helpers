<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Responses;

use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

final readonly class ModelResponse extends BaseApiResponse
{
    public function __construct(
        private string $key,
        private JsonResource $resource,
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
                $this->key => $this->resource,
            ],
        ];
    }
}
