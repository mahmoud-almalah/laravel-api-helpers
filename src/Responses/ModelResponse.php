<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

final readonly class ModelResponse implements Responsable
{
    public function __construct(
        private string $key,
        private JsonResource $resource,
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
                    $this->key => $this->resource,
                ],
            ],
            status: $this->status
        );
    }
}
