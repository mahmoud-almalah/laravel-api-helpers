# Laravel API Helpers

[![Tests](https://github.com/mahmoud-almalah/laravel-api-helpers/actions/workflows/test.yml/badge.svg)](https://github.com/mahmoud-almalah/laravel-api-helpers/actions)
[![Packagist](https://img.shields.io/packagist/v/mahmoud-almalah/laravel-api-helpers)](https://packagist.org/packages/mahmoud-almalah/laravel-api-helpers)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A clean and elegant Laravel package that provides a consistent and customizable structure for your API responses. Perfect for building maintainable, testable, and user-friendly APIs.

---

## ✨ Features

- ✅ Consistent JSON response format
- ✅ Localization via `Accept-Language` header (optional middleware)
- ✅ Built-in support for:
  - Collections with or without pagination
  - Single models/resources
  - Simple messages
  - Form request validation errors
- ✅ Laravel `Responsable` support
- ✅ Customizable success/error codes and messages
- ✅ Full test coverage with [Pest](https://pestphp.com)
- ✅ Easy to use and integrate
- ✅ Designed for Laravel 11+

---

## 📦 Installation

```bash
composer require mahmoud-almalah/laravel-api-helpers -W
```

No need to register the service provider if you’re using Laravel 5.5+ (package auto-discovery is enabled).

---

## ⚙️ Configuration

You may optionally publish the config if you need to customize success/error codes or messages:

```bash
php artisan vendor:publish --tag=laravel-api-platform-config
```

---

## 🚀 Usage

### ✅ `CollectionResponse`

Returns a list of items with optional pagination metadata.

```php
use MahmoudAlmalah\LaravelApiHelpers\Responses\CollectionResponse;
use App\Http\Resources\UserResource;

return new CollectionResponse(
    key: 'users',
    collection: UserResource::collection($users), // Collection or Paginator
    meta: $users instanceof \Illuminate\Contracts\Pagination\Paginator ? $users : null,
    message: 'Users retrieved successfully'
);
```

---

### ✅ `ModelResponse`

Wrap a single Eloquent model using a Laravel Resource.

```php
use MahmoudAlmalah\LaravelApiHelpers\Responses\ModelResponse;
use App\Http\Resources\UserResource;

return new ModelResponse(
    key: 'user',
    resource: new UserResource($user),
    message: 'User fetched successfully'
);
```

---

### ✅ `MessageResponse`

Send a message-only response with optional data.

```php
use MahmoudAlmalah\LaravelApiHelpers\Responses\MessageResponse;

return new MessageResponse(
    data: ['additional' => 'info'],
    message: 'Operation completed'
);
```

---

### ❌ `FormRequestResponse`

Use this for form validation errors (typically in custom validation handler).

```php
use MahmoudAlmalah\LaravelApiHelpers\Responses\FormRequestResponse;

return new FormRequestResponse($validator->errors()->toArray());
```

Or you can just extend your form request from `BaseRequest`:

```php
use MahmoudAlmalah\LaravelApiHelpers\Requests\BaseRequest;

class UserRequest extends BaseRequest
{
    public function rules(): array
    {
        return [/* ... */];
    }
}
```

---

## 🌐 Localization Middleware

You can enable automatic localization of your API responses based on the `Accept-Language` request header using the `ApiLocalizationMiddleware`.

### ✅ Enable Localization

To activate the middleware globally for your API, first publish the config file:

```bash
php artisan vendor:publish --tag=laravel-api-platform-config
```

Then update the localization settings in `config/laravel-api-platform.php`:

```php
'localization' => [
    'status' => env('API_LOCALIZATION_STATUS', true), // Enable or disable localization
    'locales' => ['en', 'ar'], // Supported locales
],
```

### ✅ Environment Variables

You can also set the localization settings using environment variables in your `.env` file:

```env
API_LOCALIZATION_STATUS=true
```

If enabled, the package will automatically register a middleware that sets the app locale (and number formatting) based on the `Accept-Language` header:

```http
Accept-Language: ar
```

You can also manually assign the middleware to specific routes if preferred:

```php
Route::middleware(['api-localization'])->get('/demo', fn () => response()->json([
    'locale' => app()->getLocale(),
]));
```

The middleware automatically uses Laravel’s localization and number formatting services to ensure consistent responses based on language.

---

## ✅ Output Format

All responses follow this consistent format:

```json
{
  "status": true,
  "message": "Users retrieved successfully",
  "data": {
    "users": [/* ... */]
  },
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "has_more_pages": true
  }
}
```

- `status`: `true` for success, `false` for errors
- `message`: human-readable message
- `data`: payload
- `meta`: only shown when pagination is present

---

## ✅ Testing

This package comes with full test coverage using [Pest](https://pestphp.com).

```bash
composer test
```

---

## 🛠 Development Tools

```bash
# Code style
composer lint

# Static analysis
composer test:types

# Rector refactoring
composer refacto

# Full test suite
composer test
```

---

## 📂 Directory Structure

```
src/
├── Helpers/
│   ├── ApiResponseHelpers.php
│   Middleware/
│   ├── ApiLocalizationMiddleware.php
├── Responses/
│   ├── CollectionResponse.php
│   ├── ModelResponse.php
│   ├── MessageResponse.php
│   └── FormRequestResponse.php
├── Requests/
│   ├── BaseRequest.php
└── Providers/
    └── LaravelApiHelpersServiceProvider.php
```

---

## 🤝 Contributing

Contributions are welcome! Please read the [contributing guidelines](CONTRIBUTING.md) for more information.

---

## 📄 License

The MIT License (MIT). See [LICENSE](LICENSE.md) for more information.
