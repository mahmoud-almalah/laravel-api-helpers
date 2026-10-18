# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/)
and this project adheres to [Semantic Versioning](https://semver.org/).

## [2.2.0] - Unreleased
The JSON response shape is unchanged: `success`, `message`, `data` (+ `meta` for collections, `errors` for validation, `debug` outside production).

### Added
- `ApiResponse::created()`, `notFound()`, `unauthorized()` and `forbidden()` shortcuts.
- `ApiExceptionHandler::register($exceptions)` registers the handler for `api/*` and JSON requests in one line (paths are configurable).
- `messages` config group (`validation`, `not_found`, `unauthenticated`, `forbidden`, `server_error`) so exception messages can be customised or translated.
- Opt-in `pagination.detect` config: `ApiResponse::collection()` reads the paginator from the resource when `paginator` is omitted.
- Opt-in `pagination.include_totals` config: adds `total` and `last_page` to `meta` for length aware paginators.
- `ApiExceptionHandler` maps a raw `AuthorizationException` to `403` (it was `500`).
- `.gitattributes` forces LF line endings and CI now runs the Rector check.
- `ApiExceptionHandler::render()` accepts an optional `Request`; it defaults to the current request.
- `ApiResponse::collection()` / `CollectionResponse` accept any `ResourceCollection` (e.g. your own `UserCollection`), not only `AnonymousResourceCollection`.

### Changed
- The default success/error message is now resolved once in `BaseApiResponse` instead of being copy-pasted in every response class.
- `ApiExceptionHandler` returns the HTTP status text (e.g. `Not Found`) when an exception has an empty message, instead of an empty string.
- Exception codes outside `100-599` (or non-numeric ones) fall back to `500`.
- Debug traces no longer include call arguments, and `password`, `password_confirmation`, `current_password` and `token` are removed from the debug `input`.

### Fixed
- A `NotFoundHttpException` wrapping a `ModelNotFoundException` (what Laravel passes to render callbacks) now returns `Resource not found` instead of leaking the model class name.
- The `queries` debug entry was never returned because the query log check used `method_exists()` on the `DB` facade; it now works when the query log is enabled.
- Removed Mockery-specific code from `BaseApiResponse` production code.

## [2.1.0] - 2026-07-12
### Added
- Abstract `BaseApiResponse` class to unify and clean up JSON structure building.
- Supported optional `$paginator` parameter in `ApiResponse::collection()` facade method for cleaner pagination.
- Added dynamic lookup of default success/error messages from config file (`api-helpers.php`), with fallback support.
- Added `@deprecated` warning to `BaseRequest::failedValidation()` to prepare for future validation consolidation.

### Fixed
- Fixed `FormRequestResponse` using the `Response::json()` facade, replacing it with `new JsonResponse()` for testing and container independence.
- Decomposed `ApiExceptionHandler::render()` method into focused helper methods to improve readability and maintainability.

## [2.0.1] - 2026-02-15
### Fixed
- Changed `HandlesApiExceptions` trait to `ApiExceptionHandler` class to properly support static calls in `bootstrap/app.php` (Fixes incorrect documentation/usage).

## [2.0.0] - 2026-02-09
### Added
- **Enhanced Exception Handling**: `ApiExceptionHandler` class with dev-mode debug info.
- `ApiResponse` static class as the unified entry point for all responses.
- `api-helpers.php` configuration file.

### Changed
- Refactored all Response classes (`MessageResponse`, `CollectionResponse`, etc.) to be strict and consistent.
- `BaseRequest` now uses the new response structure for failed validation.
- JSON response structure now uses `success` (bool) instead of `status` and puts errors under `errors`.

### Removed
- **Global Helpers**: `api_success`, `api_error`, etc.
- **Middleware**: `ApiLocalizationMiddleware`.
- Legacy configuration file `laravel-api-platform.php`.

## [1.2.0] - 2025-04-14
### Updated
- Updated `CollectionResponse` to support custom pagination formats.
- Improved error handling in `FormRequestResponse` to provide more detailed validation messages.
- Refactored `laravel-api-helpers` to use `env` for configuration values.
- Updated documentation to include examples for new features.
- Improved test coverage for edge cases in `CollectionResponse` and `ModelResponse`.

## [1.1.0] - 2025-04-14
### Added
- New middleware `ApiLocalizationMiddleware` that sets app locale based on `Accept-Language` header if enabled.

## [1.0.0] - 2025-04-14
### Added
- Initial release of `Laravel API Helpers` package
- `CollectionResponse` for paginated or plain collections
- `ModelResponse` for single resource responses
- `MessageResponse` for generic success messages
- `FormRequestResponse` for validation errors
- Full support for `Responsable` interface
- Configurable success/error codes via config file
- 100% test coverage with Pest
