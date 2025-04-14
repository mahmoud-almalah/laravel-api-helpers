# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/)
and this project adheres to [Semantic Versioning](https://semver.org/).

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
