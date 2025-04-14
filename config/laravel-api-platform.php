<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\Response;

return [
    'code' => [
        'success' => [
            Response::HTTP_OK,
            Response::HTTP_CREATED,
            Response::HTTP_ACCEPTED,
            Response::HTTP_NO_CONTENT,
            Response::HTTP_RESET_CONTENT,
            Response::HTTP_PARTIAL_CONTENT,
            Response::HTTP_MULTI_STATUS,
        ],
        'error' => [
            Response::HTTP_BAD_REQUEST,
            Response::HTTP_UNAUTHORIZED,
            Response::HTTP_FORBIDDEN,
            Response::HTTP_NOT_FOUND,
            Response::HTTP_METHOD_NOT_ALLOWED,
            Response::HTTP_NOT_ACCEPTABLE,
            Response::HTTP_PROXY_AUTHENTICATION_REQUIRED,
            Response::HTTP_REQUEST_TIMEOUT,
            Response::HTTP_CONFLICT,
            Response::HTTP_GONE,
            Response::HTTP_LENGTH_REQUIRED,
            Response::HTTP_PRECONDITION_FAILED,
            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
            Response::HTTP_EXPECTATION_FAILED,
        ],
    ],

    'messages' => [
        'success' => 'Success',
        'validation' => 'Validation failed',
        'not_found' => 'Resource not found',
        'error' => 'An error occurred',
    ],

    'localization' => [
        'status' => true,
        'locales' => [
            'en',
            'ar',
            'fr',
            'de',
            'es',
            'it',
            'pt',
            'ru',
            'zh',
        ],
    ],
];
