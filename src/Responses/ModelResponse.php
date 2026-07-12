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
                $this->key => $this->resource,
            ],
        ];
    }
}
