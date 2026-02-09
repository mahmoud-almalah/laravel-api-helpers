<?php

declare(strict_types=1);

namespace Tests\Stubs;

use Illuminate\Foundation\Http\FormRequest;

final class TestRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email',
            'age' => 'integer',
        ];
    }
}
