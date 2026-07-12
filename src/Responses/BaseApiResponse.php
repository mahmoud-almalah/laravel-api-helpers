<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Throwable;

abstract readonly class BaseApiResponse implements Responsable
{
    public function __construct(
        protected string $message,
        protected int $status,
    ) {}

    /**
     * @return array<string, mixed>
     */
    abstract protected function buildPayload(): array;

    public function toResponse($request): JsonResponse
    {
        return new JsonResponse(
            data: $this->buildPayload(),
            status: $this->status,
        );
    }

    protected static function resolveConfig(string $key, string $default): string
    {
        $container = \Illuminate\Container\Container::getInstance();

        if ($container instanceof \Mockery\MockInterface) {
            return $default;
        }

        try {
            if ($container->bound('config')) {
                $value = config($key, $default);

                return is_string($value) ? $value : $default;
            }
        } catch (Throwable) {
        }

        return $default;
    }

    protected function isSuccessful(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }
}
