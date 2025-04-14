<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use MahmoudAlmalah\LaravelApiHelpers\Responses\FormRequestResponse;
use Override;

abstract class BaseRequest extends FormRequest
{
    #[Override]
    final protected function failedValidation(Validator $validator): void
    {
        /** @var array<string, array<int, string>> $errors */
        $errors = $validator->errors()->toArray();

        throw new HttpResponseException(
            response: (new FormRequestResponse(
                message: $validator->errors()->first(),
                data: $errors
            ))->toResponse($this)
        );
    }
}
