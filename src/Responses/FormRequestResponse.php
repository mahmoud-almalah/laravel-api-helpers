<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Responses;

use MahmoudAlmalah\LaravelApiHelpers\Support\ApiConfig;
use Symfony\Component\HttpFoundation\Response;

final readonly class FormRequestResponse extends BaseApiResponse
{
    /**
     * @param  array<string, array<int, string>>  $data  Deprecated name, will be renamed to $errors in v3.0
     */
    public function __construct(
        ?string $message,
        private array $data
    ) {
        parent::__construct(
            $message ?? ApiConfig::message('validation'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    protected function buildPayload(): array
    {
        return [
            'success' => false,
            'message' => $this->message,
            'errors' => $this->data,
        ];
    }
}
