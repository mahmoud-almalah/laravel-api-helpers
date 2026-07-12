---
name: use-laravel-api-helpers
description: Guidelines for building API responses and handling exceptions using the MahmoudAlmalah/LaravelApiHelpers package.
---

# Laravel API Helpers Usage Guidelines

When working in a project that uses `mahmoud-almalah/laravel-api-helpers`, you must follow these rules for generating API responses and handling exceptions.

## 1. Responses

Always use the `MahmoudAlmalah\LaravelApiHelpers\Responses\ApiResponse` factory to return JSON responses. 
**Never** use Laravel's default `response()->json()` or custom response arrays.

### Success Response
```php
use MahmoudAlmalah\LaravelApiHelpers\Responses\ApiResponse;

return ApiResponse::success(
    data: ['foo' => 'bar'], 
    message: 'Operation successful' // optional
);
```

### Error Response
```php
return ApiResponse::error(
    message: 'Something went wrong', 
    status: 500, // optional, defaults to 500
    data: ['error_code' => 123] // optional
);
```

### Model / Single Resource Response
Use this when returning a single Eloquent model wrapped in a `JsonResource`.
```php
return ApiResponse::model(
    key: 'user', 
    resource: new UserResource($user)
);
```

### Collection / Paginated Response
Use this when returning a collection of resources, optionally with a paginator.
```php
return ApiResponse::collection(
    key: 'users', 
    resource: UserResource::collection($users), 
    paginator: $users // optional, passing the paginator populates the `meta` response key
);
```

## 2. Exception Handling

For the API to return standardized errors automatically, exceptions must be intercepted by the package's handler.

In Laravel 11 (`bootstrap/app.php`):
```php
use MahmoudAlmalah\LaravelApiHelpers\Exceptions\ApiExceptionHandler;

->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (\Throwable $e, Request $request) {
        if ($request->is('api/*') || $request->expectsJson()) {
            return ApiExceptionHandler::render($e);
        }
    });
})
```

## 3. Validation

To utilize the package's standard validation error format, simply create your FormRequests normally. Ensure your API requests expect JSON. If the package's `BaseRequest` is available, you may extend it, though standard Laravel `FormRequest` validation exceptions will also be caught by `ApiExceptionHandler` if configured as shown above.

## 4. Configuration

The response messages fallback to configuration. To customize the default success/error messages, ensure `config/api-helpers.php` is published and modified.
