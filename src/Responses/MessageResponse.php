<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

final readonly class MessageResponse implements Responsable
{
    public function __construct(
        /** @var array<string, mixed>|null $data */
        private ?array $data = null,
        private string $message = 'Success',
        private int $status = Response::HTTP_OK,
    ) {}

    public function toResponse($request): JsonResponse
    {
        return new JsonResponse(
            data: [
                'status' => in_array($this->status, Config::array('laravel-api-platform.code.success'), true),
                'message' => $this->message,
                'data' => $this->data,
            ],
            status: $this->status
        );
    }
}
