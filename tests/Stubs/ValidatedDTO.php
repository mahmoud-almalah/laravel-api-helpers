<?php

declare(strict_types=1);

namespace Tests\Stubs;

use MahmoudAlmalah\LaravelApiHelpers\DTO\DataTransferObject;

final class ValidatedDTO extends DataTransferObject
{
    public string $name;

    public int $age;

    public static function rules(): array
    {
        return [
            'name' => 'required|string',
            'age' => 'required|integer|min:18',
        ];
    }
}
