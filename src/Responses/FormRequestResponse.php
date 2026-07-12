<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Responses;

use Symfony\Component\HttpFoundation\Response;

final readonly class FormRequestResponse extends BaseApiResponse
{
    public function __construct(
        ?string $message,
        /**
         * @var array<string, array<int, string>> $data
         *
         * @deprecated Parameter will be renamed to $errors in v3.0
         */
        private array $data
    ) {
        parent::__construct(
            $message ?? 'Validation failed',
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
