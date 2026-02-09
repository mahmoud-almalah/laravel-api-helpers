<?php

declare(strict_types=1);

use Tests\Stubs\TestDTO;
use Tests\Stubs\TestRequest;

it('creates a DTO from an array', function (): void {
    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'age' => 30,
    ];

    $dto = TestDTO::fromRequest($data);

    expect($dto)->toBeInstanceOf(TestDTO::class)
        ->and($dto->name)->toBe('John Doe')
        ->and($dto->email)->toBe('john@example.com')
        ->and($dto->age)->toBe(30);
});

it('creates a DTO from a request', function (): void {
    $data = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'age' => 25,
    ];

    $request = TestRequest::create('/test', 'POST', $data);

    // Mock validation
    $request->setContainer(app());
    $request->validateResolved();

    $dto = TestDTO::fromRequest($request);

    expect($dto)->toBeInstanceOf(TestDTO::class)
        ->and($dto->name)->toBe('Jane Doe')
        ->and($dto->email)->toBe('jane@example.com')
        ->and($dto->age)->toBe(25);
});

it('converts DTO to array', function (): void {
    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'age' => 30,
    ];

    $dto = TestDTO::fromRequest($data);
    $array = $dto->toArray();

    expect($array)->toBe($data);
});
