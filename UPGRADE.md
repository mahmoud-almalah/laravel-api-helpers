# Upgrade Guide

## Upgrading To 2.0.0

Version 2.0.0 acts as a major refactor of the package, introducing strict typing, new features, and cleaning up the codebase. This version introduces breaking changes, so please follow this guide to upgrade your application.

### High Impact Changes

#### ❌ Helpers & Middleware Removed
- Global helper functions (`api_success`, `api_error`, `api_model_response`, `api_collection_response`, `api_validation_error`) have been **removed** to keep the global namespace clean.
- `ApiLocalizationMiddleware` has been **removed**. The package now focuses solely on API responses and helpers, leaving localization to specialized packages or application-level middleware.

#### ⚠️ Response Structure Changes
- The `status` field in the JSON response is now `success` (boolean).
- Validation errors are now returned under the `errors` key (standard Laravel structure) instead of `data`.
- Configuration file has been renamed from `laravel-api-platform.php` to `api-helpers.php`.

### Upgrade Steps

#### 1. Update Composer Dependency
Update your `composer.json` to require the new version:

```json
"require": {
    "mahmoud-almalah/laravel-api-helpers": "^2.0"
}
```

Then run:
```bash
composer update mahmoud-almalah/laravel-api-helpers
```

#### 2. Exception Handling

Previously, there was no standard exception handler. Now, use `ApiExceptionHandler::render($e)` in your exception handler configuration.

#### Before (v1.x):
Likely manually handling exceptions or utilizing global handlers.

#### After (v2.0):
In `bootstrap/app.php` (Laravel 11+):

```php
use MahmoudAlmalah\LaravelApiHelpers\Exceptions\ApiExceptionHandler;

// ...
->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (Throwable $e, Request $request) {
        if ($request->is('api/*')) {
            return ApiExceptionHandler::render($e);
        }
    });
})
```

#### 3. Publish New Configuration
The configuration file has changed. You should publish the new one:

```bash
php artisan vendor:publish --tag=api-helpers-config
```

#### 4. Replace Helpers with `ApiResponse`
You must replace all usages of the deprecated helper functions with the `MahmoudAlmalah\LaravelApiHelpers\Responses\ApiResponse` class.

**Search & Replace:**

- `api_success($data)` -> `ApiResponse::success($data)`
- `api_error($message, $status)` -> `ApiResponse::error($message, $status)`
- `api_model_response($resource)` -> `ApiResponse::model('key', $resource)`
- `api_collection_response($resource)` -> `ApiResponse::collection('key', $resource)`

#### 5. Update Frontend/Clients
Your API consumers typically need to update how they check for success.

**Previous Response:**
```json
{
    "status": true,
    "data": { ... }
}
```

**New Response:**
```json
{
    "success": true,
    "message": "...",
    "data": { ... }
}
```

#### 6. Adopt New Features (Optional)
You can now leverage:
- **DTOs**: `MahmoudAlmalah\LaravelApiHelpers\DTO\DataTransferObject`
- **Filtering**: `use HasApiFilters` in your models.
- **Exceptions**: Use `ApiExceptionHandler` in your exception handler.
