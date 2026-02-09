<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\DTO;

use MahmoudAlmalah\LaravelApiHelpers\Concerns\MappedFromRequest;

abstract class DataTransferObject
{
    use MappedFromRequest;

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    final public function toArray(): array
    {
        return get_object_vars($this);
    }
}
