# Laravel API Helpers

[![Tests](https://github.com/mahmoud-almalah/laravel-api-helpers/actions/workflows/test.yml/badge.svg)](https://github.com/mahmoud-almalah/laravel-api-helpers/actions)
[![Packagist](https://img.shields.io/packagist/v/mahmoud-almalah/laravel-api-helpers)](https://packagist.org/packages/mahmoud-almalah/laravel-api-helpers)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A clean and elegant Laravel package that provides a consistent and customizable structure for your API responses. Perfect for building maintainable, testable, and user-friendly APIs.

---

## ✨ Features

- ✅ Consistent JSON response format
- ✅ Built-in support for:
  - Collections with or without pagination
  - Single models/resources
  - Simple messages
  - Form request validation errors
- ✅ Laravel `Responsable` support
- ✅ Designed for Laravel 11+

---

## 📦 Installation

```bash
composer require mahmoud-almalah/laravel-api-helpers
```

No need to register the service provider if you’re using Laravel 5.5+ (package auto-discovery is enabled).

---

## ⚙️ Configuration

You may optionally publish the config if you need to customize success/error codes or messages:

```bash
php artisan vendor:publish --tag=laravel-api-helpers-config
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
    paginator: $users instanceof \Illuminate\Contracts\Pagination\Paginator ? $users : null,
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
├── Responses/
│   ├── CollectionResponse.php
│   ├── ModelResponse.php
│   ├── MessageResponse.php
│   └── FormRequestResponse.php
└── Providers/
    └── LaravelApiHelpersServiceProvider.php
```

---

## 🤝 Contributing

Pull requests and issues are welcome. Make sure your code passes tests and follows the PSR-12 style guide.

---

## 📄 License

The MIT License (MIT). See [LICENSE](LICENSE) for more information.
```

---

لو تحب، أقدر أضيف:
- أمثلة real-world بالـ controller
- توضيح كيف تدمجها مع `ExceptionHandler` أو `FormRequest`

هل تحب أجهزلك ملف `LICENSE`, `CONTRIBUTING.md`, و `.github/workflows/test.yml` أيضًا؟
