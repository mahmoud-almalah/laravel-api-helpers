<?php

declare(strict_types=1);

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

pest()->use(Tests\TestCase::class);

/**
 * The decoded JSON body of a package response.
 *
 * @return array<string, mixed>
 */
function bodyOf(Responsable $response): array
{
    /** @var JsonResponse $json */
    $json = $response->toResponse(new Request());

    /** @var array<string, mixed> $data */
    $data = $json->getData(true);

    return $data;
}
