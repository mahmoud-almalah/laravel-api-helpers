<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Responses;

use Symfony\Component\HttpFoundation\Response;

final readonly class MessageResponse extends BaseApiResponse
{
    /**
     * @param  array<string, mixed>|null  $data
     * @param  array<string, mixed>|null  $debug
     */
    public function __construct(
        private ?array $data = null,
        ?string $message = null,
        int $status = Response::HTTP_OK,
        private ?array $debug = null,
    ) {
        parent::__construct($message, $status);
    }

    protected function buildPayload(): array
    {
        $payload = [
            'success' => $this->isSuccessful(),
            'message' => $this->message,
            'data' => $this->data,
        ];

        if ($this->debug !== null) {
            $payload['debug'] = $this->debug;
        }

        return $payload;
    }
}
