<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | API Defaults
    |--------------------------------------------------------------------------
    |
    | Messages used when a response is created without an explicit message.
    |
    */

    'defaults' => [
        'message' => 'Success',
        'error_message' => 'An error occurred',
    ],

    /*
    |--------------------------------------------------------------------------
    | Exception Messages
    |--------------------------------------------------------------------------
    |
    | Messages returned by ApiExceptionHandler and the shortcut responses.
    | To translate them, override these keys at runtime (for example in a
    | service provider: config(['api-helpers.messages.not_found' => __('...')])).
    |
    */

    'messages' => [
        'validation' => 'Validation failed',
        'not_found' => 'Resource not found',
        'unauthenticated' => 'Unauthenticated',
        'forbidden' => 'Forbidden',
        'server_error' => 'Server Error',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | Both options are off by default so the JSON shape stays exactly the same.
    |
    | detect:         ApiResponse::collection() reads the paginator from the
    |                 resource when the `paginator` argument is omitted.
    | include_totals: add `total` and `last_page` to `meta` when the paginator
    |                 knows them (length aware paginators).
    |
    */

    'pagination' => [
        'detect' => false,
        'include_totals' => false,
    ],
];
