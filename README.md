# Laravel API Helpers

[![Tests](https://github.com/mahmoud-almalah/laravel-api-helpers/actions/workflows/test.yml/badge.svg)](https://github.com/mahmoud-almalah/laravel-api-helpers/actions)
[![Packagist](https://img.shields.io/packagist/v/mahmoud-almalah/laravel-api-helpers)](https://packagist.org/packages/mahmoud-almalah/laravel-api-helpers)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A clean and elegant Laravel package that provides a consistent and customizable structure for your API development. It includes standardized response classes and exception handling.

---

## ✨ Features

- ✅ **Consistent JSON Responses** for success, errors, collections, and resources.
- ✅ **Standardized Exception Handling** via `ApiExceptionHandler` class.
- ✅ **Strict Typing** and architecture built around extending `BaseApiResponse`.
- ✅ **Laravel 11+** Support.
- ✅ Full test coverage with [Pest](https://pestphp.com) and Max Level PHPStan.

---

## 📦 Installation

```bash
composer require mahmoud-almalah/laravel-api-helpers
```

---

## ⚙️ Configuration

You can publish the configuration file to customize the internal settings:

```bash
php artisan vendor:publish --tag=api-helpers-config
```

This will publish `config/api-helpers.php`.

---

## 🚀 Usage

### 1️⃣ Standardized Responses

Use the `ApiResponse` factory to return consistent JSON responses.

#### Success Response
```php
use MahmoudAlmalah\LaravelApiHelpers\Responses\ApiResponse;

public function index()
{
    return ApiResponse::success(
        data: ['foo' => 'bar'],
        message: 'Operation successful'
    );
}
```

#### Error Response
```php
use MahmoudAlmalah\LaravelApiHelpers\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

public function error()
{
    return ApiResponse::error(
        message: 'Something went wrong',
        status: Response::HTTP_BAD_REQUEST
    );
}
```

#### Resource/Model Response
Wraps your Eloquent model or JsonResource.

```php
use MahmoudAlmalah\LaravelApiHelpers\Responses\ApiResponse;
use App\Http\Resources\UserResource;

public function show(User $user)
{
    return ApiResponse::model(
        key: 'user',
        resource: new UserResource($user),
        message: 'User retrieved successfully'
    );
}
```

#### Collection Response
Handles pagination metadata automatically.

```php
use MahmoudAlmalah\LaravelApiHelpers\Responses\ApiResponse;
use App\Http\Resources\UserResource;

public function index()
{
    $users = User::paginate(10);
    
    return ApiResponse::collection(
        key: 'users',
        resource: UserResource::collection($users),
        paginator: $users,
        message: 'Users list'
    );
}
```

---

### 2️⃣ Standardized Exception Handling

Catch exceptions and return consistent JSON error responses, including detailed debug info in local development.

**Setup in `bootstrap/app.php` (Laravel 11+):**

```php
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use MahmoudAlmalah\LaravelApiHelpers\Exceptions\ApiExceptionHandler;

->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (\Throwable $e, Request $request) {
        if ($request->is('api/*') || $request->expectsJson()) {
            return ApiExceptionHandler::render($e);
        }
    });
})
```

**Debug Info (Local Environment):**
When `APP_ENV=local`, exceptions will include debug details:

```json
{
    "success": false,
    "message": "Call to undefined method App\\Models\\User::unknown()",
    "data": null,
    "debug": {
        "exception": {
            "class": "BadMethodCallException",
            "file": "/var/www/html/app/Http/Controllers/UserController.php",
            "line": 45,
            "trace": [...]
        },
        "request": {
            "method": "GET",
            "url": "http://localhost/api/users",
            "input": []
        },
        "time": "2023-10-25T14:30:00+00:00"
    }
}
```

In **Production**, it safely returns:
```json
{
    "success": false,
    "message": "Server Error",
    "data": null
}
```

---

## ✅ Output Format

Success Response:
```json
{
  "success": true,
  "message": "Users list",
  "data": {
    "users": [
      ...
    ]
  },
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "has_more_pages": true
  }
}
```

Error Response:
```json
{
  "success": false,
  "message": "Resource not found",
  "data": null
}
```

---

## 🧪 Testing

Run the test suite:

```bash
composer test
```

Run static analysis:

```bash
composer test:types
```

## 📄 License

The MIT License (MIT). See [LICENSE](LICENSE) for more information.
