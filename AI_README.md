# AI Context & Coding Standards

This file provides context for AI agents working on the `laravel-api-helpers` package. It outlines the architectural decisions, coding standards, and preferred patterns.

## 1. Package Overview
A strictly typed, opinionated Laravel package for building standardized APIs. It avoids global helpers in favor of static classes and standardized Response objects.

**Core Philosophy:**
- **Strict Typing:** Use PHP types everywhere.
- **No Magic:** Avoid magic methods (`__call`, `__get`) where possible.
- **Standardization:** All responses must follow the defined JSON structure.

## 2. Key Components

### A. Responses (`src/Responses/`)
Always use the `MahmoudAlmalah\LaravelApiHelpers\Responses\ApiResponse` static class factory.
- **Success:** `ApiResponse::success($data, $message)`
- **Created (201):** `ApiResponse::created($data, $message)`
- **Error:** `ApiResponse::error($message, $status)`
- **Model:** `ApiResponse::model('key', $resource)`
- **Collection:** `ApiResponse::collection('key', $resource)`

**Do NOT use:**
- `response()->json()` manually.
- Old helper functions (`api_success`, etc.).

### B. Exceptions (`src/Exceptions/`)
- Register with `ApiExceptionHandler::register($exceptions)` in `bootstrap/app.php`, or call `ApiExceptionHandler::render($e, $request)` from your own renderer.
- Shortcuts: `ApiResponse::notFound()`, `unauthorized()`, `forbidden()`. Built-in messages live in `lang/{en,ar}/messages.php` (`api-helpers::messages.*`) and are resolved through `ApiConfig::message()`; never hardcode user-facing strings, add a key to every lang file instead.
- `pagination.detect` and `pagination.include_totals` are opt-in config flags and must stay `false` by default.

### C. JSON contract (never change without a major version)
`{ success, message, data }`, plus `meta` (collections), `errors` (validation) and `debug` (non-production errors). Default messages are resolved in `BaseApiResponse`; new response types must extend it and only implement `buildPayload()`.

## 3. Testing Standards
- **Framework:** [Pest PHP](https://pestphp.com).
- **Style:** Use expectation API (`expect($val)->toBe(...)`).
- **Coverage:** Maintain 100% Type Coverage (`composer test:types`).
- **Mocking:** Use `Mockery` for strictly typed mocks. Avoid partial mocks if possible.

## 4. Workflows asking for "Modification"
When asked to modify code:
1.  **Check existing tests** to understand expected behavior.
2.  **Run static analysis** (`composer test:types`) before and after changes.
3.  **Prefer Composition over Inheritance**.
4.  **Avoid breaking changes** unless explicitly requested (this is a library, semver matters).

## 5. Common Pitfalls
- **PHPStan Errors:** The package runs on `level: max`. Do not suppress errors unless absolutely necessary.
- **ReturnTypeWillChange:** Ensure all interface implementations strictly match return types.
- **Facades:** Avoid facades in core logic if dependency injection is cleaner, but they are acceptable for Laravel integration.
