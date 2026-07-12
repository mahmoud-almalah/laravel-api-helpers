<?php

declare(strict_types=1);

namespace Tests\Stubs;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read int|string $id
 * @property-read string $name
 */
final class DummyResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        if (is_array($this->resource)) {
            return $this->resource;
        }

        return ['id' => $this->id, 'name' => $this->name];
    }
}
