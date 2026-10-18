# Laravel API Helpers

[![Tests](https://github.com/mahmoud-almalah/laravel-api-helpers/actions/workflows/tests.yml/badge.svg)](https://github.com/mahmoud-almalah/laravel-api-helpers/actions)
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

This will publish `config/api-helpers.php`:

| Key | Default | Purpose |
| --- | --- | --- |
| `defaults.message` / `defaults.error_message` | `Success` / `An error occurred` | Message when none is given |
| `messages.*` | `Validation failed`, `Resource not found`, `Unauthenticated`, `Forbidden`, `Server Error` | Exception and shortcut messages |
| `pagination.detect` | `false` | Read the paginator from the resource when `paginator:` is omitted |
| `pagination.include_totals` | `false` | Add `total` and `last_page` to `meta` (length aware paginators) |

The defaults keep the JSON shape exactly as documented below. To translate messages, set them at runtime, for example in a service provider's `boot()`:

```php
config(['api-helpers.messages.not_found' => __('Resource not found')]);
```

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

#### Created Response
```php
return ApiResponse::created(
    data: ['id' => $user->id],
    message: 'User created'
); // HTTP 201
```

#### Status Shortcuts
```php
return ApiResponse::notFound();      // 404, "Resource not found"
return ApiResponse::unauthorized();  // 401, "Unauthenticated"
return ApiResponse::forbidden();     // 403, "Forbidden"
return ApiResponse::notFound('No such user'); // custom message
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
use MahmoudAlmalah\LaravelApiHelpers\Exceptions\ApiExceptionHandler;

->withExceptions(function (Exceptions $exceptions) {
    ApiExceptionHandler::register($exceptions); // api/* and JSON requests
    // ApiExceptionHandler::register($exceptions, ['api/*', 'webhooks/*']);
})
```

Need full control instead? Call `ApiExceptionHandler::render($e, $request)` from your own `$exceptions->render(...)` callback.

Handled out of the box: validation (`422`), model not found (`404`), unauthenticated (`401`) and any other exception (status taken from the exception, `500` otherwise).

**Debug Info (Local Environment):**
When the app is not in production, exceptions include debug details (call arguments and sensitive input such as `password` are never included, and `queries` is added when the query log is enabled):

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
