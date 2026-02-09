<?php

declare(strict_types=1);

namespace Tests\Stubs;

use MahmoudAlmalah\LaravelApiHelpers\DTO\DataTransferObject;

final class TestDTO extends DataTransferObject
{
    public string $name;

    public string $email;

    public int $age;
}
