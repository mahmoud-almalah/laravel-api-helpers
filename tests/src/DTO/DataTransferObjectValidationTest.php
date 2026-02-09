<?php

declare(strict_types=1);

use Illuminate\Validation\ValidationException;
use Tests\Stubs\ValidatedDTO;

it('validates DTO data successfully', function (): void {
    $data = [
        'name' => 'John Doe',
        'age' => 25,
    ];

    $dto = ValidatedDTO::fromRequest($data);

    expect($dto)->toBeInstanceOf(ValidatedDTO::class)
        ->and($dto->name)->toBe('John Doe')
        ->and($dto->age)->toBe(25);
});

it('throws validation exception for invalid DTO data', function (): void {
    $data = [
        'name' => 'John Doe',
        'age' => 10, // Invalid age
    ];

    ValidatedDTO::fromRequest($data);
})->throws(ValidationException::class);

it('throws validation exception for missing DTO data', function (): void {
    $data = [
        'name' => 'John Doe',
        // Missing age
    ];

    ValidatedDTO::fromRequest($data);
})->throws(ValidationException::class);
