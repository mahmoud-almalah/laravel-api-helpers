<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Responses;

use Symfony\Component\HttpFoundation\Response;

final readonly class MessageResponse extends BaseApiResponse
{
    public function __construct(
        /** @var array<string, mixed>|null $data */
        private ?array $data = null,
        ?string $message = null,
        int $status = Response::HTTP_OK,
        /** @var array<string, mixed>|null $debug */
        private ?array $debug = null,
    ) {
        $message ??= ($status >= 200 && $status < 300)
            ? self::resolveConfig('api-helpers.defaults.message', 'Success')
            : self::resolveConfig('api-helpers.defaults.error_message', 'An error occurred');

        parent::__construct($message, $status);
    }

    protected function buildPayload(): array
    {
        $response = [
            'success' => $this->isSuccessful(),
            'message' => $this->message,
            'data' => $this->data,
        ];

        if ($this->debug !== null) {
            $response['debug'] = $this->debug;
        }

        return $response;
    }
}
