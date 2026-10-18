<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use MahmoudAlmalah\LaravelApiHelpers\Support\ApiConfig;

abstract readonly class BaseApiResponse implements Responsable
{
    protected string $message;

    /**
     * @param  string|null  $message  Falls back to the configured success/error message when null.
     */
    public function __construct(?string $message, protected int $status)
    {
        $this->message = $message ?? $this->defaultMessage($status);
    }

    /**
     * The full JSON body of the response.
     *
     * @return array<string, mixed>
     */
    abstract protected function buildPayload(): array;

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function toResponse($request): JsonResponse
    {
        return new JsonResponse(
            data: $this->buildPayload(),
            status: $this->status,
        );
    }

    protected function isSuccessful(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }

    private function defaultMessage(int $status): string
    {
        return $status >= 200 && $status < 300
            ? ApiConfig::message('success', 'api-helpers.defaults.message')
            : ApiConfig::message('error', 'api-helpers.defaults.error_message');
    }
}
