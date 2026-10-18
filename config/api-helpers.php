<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Messages
    |--------------------------------------------------------------------------
    |
    | Messages come from the package translations (lang/{locale}/messages.php),
    | so they follow the current locale. Publish them to edit or add a language:
    |
    |     php artisan vendor:publish --tag=api-helpers-lang
    |
    | Set a value here only to force one fixed text for every locale.
    | `null` means "use the translation".
    |
    */

    'defaults' => [
        'message' => null,
        'error_message' => null,
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
