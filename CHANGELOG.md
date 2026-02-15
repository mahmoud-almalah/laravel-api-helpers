# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/)
and this project adheres to [Semantic Versioning](https://semver.org/).

## [2.0.1] - 2026-02-15
### Fixed
- Changed `HandlesApiExceptions` trait to `ApiExceptionHandler` class to properly support static calls in `bootstrap/app.php` (Fixes incorrect documentation/usage).

## [2.0.0] - 2026-02-09
### Added
- **Data Transfer Objects (DTO)** support with strict typing and built-in validation.
- **API Query Filtering**: `HasApiFilters` trait for model filtering and sorting via query parameters.
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
