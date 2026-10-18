<?php

declare(strict_types=1);

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use MahmoudAlmalah\LaravelApiHelpers\Exceptions\ApiExceptionHandler;
use MahmoudAlmalah\LaravelApiHelpers\Responses\ApiResponse;
use MahmoudAlmalah\LaravelApiHelpers\Support\ApiConfig;

function exceptionMessage(Throwable $e): mixed
{
    /** @var array<string, mixed> $body */
    $body = json_decode((string) ApiExceptionHandler::render($e, Request::create('/'))->getContent(), true);

    return $body['message'];
}

it('uses the english messages by default', function (): void {
    expect(bodyOf(ApiResponse::success())['message'])->toBe('Success')
        ->and(bodyOf(ApiResponse::error(status: 400))['message'])->toBe('An error occurred')
        ->and(bodyOf(ApiResponse::unauthorized())['message'])->toBe('Unauthenticated')
        ->and(bodyOf(ApiResponse::forbidden())['message'])->toBe('Forbidden')
        ->and(bodyOf(ApiResponse::notFound())['message'])->toBe('Resource not found')
        ->and(bodyOf(ApiResponse::validation(['a' => ['b']]))['message'])->toBe('Validation failed');
});

it('ships arabic messages', function (): void {
    app()->setLocale('ar');

    expect(bodyOf(ApiResponse::success())['message'])->toBe('تمت العملية بنجاح')
        ->and(bodyOf(ApiResponse::error(status: 400))['message'])->toBe('حدث خطأ')
        ->and(bodyOf(ApiResponse::unauthorized())['message'])->toBe('غير مسجل الدخول')
        ->and(bodyOf(ApiResponse::forbidden())['message'])->toBe('غير مسموح بالوصول')
        ->and(bodyOf(ApiResponse::notFound())['message'])->toBe('المورد غير موجود')
        ->and(bodyOf(ApiResponse::validation(['a' => ['b']]))['message'])->toBe('فشل التحقق من البيانات');
});

it('follows the locale of each request', function (): void {
    app()->setLocale('ar');
    $arabic = bodyOf(ApiResponse::unauthorized())['message'];

    app()->setLocale('en');

    expect($arabic)->toBe('غير مسجل الدخول')
        ->and(bodyOf(ApiResponse::unauthorized())['message'])->toBe('Unauthenticated');
});

it('falls back to english for locales without translations', function (): void {
    app()->setLocale('fr');
    app()->setFallbackLocale('en');

    expect(bodyOf(ApiResponse::unauthorized())['message'])->toBe('Unauthenticated');
});

it('translates exception responses', function (): void {
    app()->setLocale('ar');

    expect(exceptionMessage(new AuthenticationException()))->toBe('غير مسجل الدخول')
        ->and(exceptionMessage(new ModelNotFoundException()))->toBe('المورد غير موجود')
        ->and(exceptionMessage(new AuthorizationException()))->toBe('This action is unauthorized.');

    app()['env'] = 'production';

    expect(exceptionMessage(new Exception('Secret', 500)))->toBe('خطأ في الخادم');
});

it('lets projects override a message with their own translation', function (): void {
    /** @var Illuminate\Translation\Translator $translator */
    $translator = app('translator');
    $translator->addLines(['messages.not_found' => 'Nothing here'], 'en', 'api-helpers');

    expect(bodyOf(ApiResponse::notFound())['message'])->toBe('Nothing here');
});

it('does not translate explicit messages', function (): void {
    app()->setLocale('ar');

    expect(bodyOf(ApiResponse::notFound('Forbidden'))['message'])->toBe('Forbidden')
        ->and(bodyOf(ApiResponse::success(message: 'Success'))['message'])->toBe('Success');
});

it('lets the defaults config force a fixed message', function (): void {
    config(['api-helpers.defaults.message' => 'Done', 'api-helpers.defaults.error_message' => 'Failed']);
    app()->setLocale('ar');

    expect(bodyOf(ApiResponse::success())['message'])->toBe('Done')
        ->and(bodyOf(ApiResponse::error(status: 400))['message'])->toBe('Failed');
});

it('still translates when an old published config holds the english defaults', function (): void {
    config(['api-helpers.defaults.message' => 'Success', 'api-helpers.defaults.error_message' => 'An error occurred']);
    app()->setLocale('ar');

    expect(bodyOf(ApiResponse::success())['message'])->toBe('تمت العملية بنجاح')
        ->and(bodyOf(ApiResponse::error(status: 400))['message'])->toBe('حدث خطأ');
});

it('ignores empty and non string overrides', function (): void {
    config(['api-helpers.defaults.message' => '', 'api-helpers.defaults.error_message' => ['x']]);

    expect(bodyOf(ApiResponse::success())['message'])->toBe('Success')
        ->and(bodyOf(ApiResponse::error(status: 400))['message'])->toBe('An error occurred');
});

it('falls back to english when the translator is unavailable', function (): void {
    $original = Container::getInstance();
    Container::setInstance(new Container());

    try {
        expect(ApiConfig::message('forbidden'))->toBe('Forbidden')
            ->and(ApiConfig::message('unknown'))->toBe('unknown');
    } finally {
        Container::setInstance($original);
    }
});

it('publishes the translations', function (): void {
    $paths = Illuminate\Support\ServiceProvider::pathsToPublish(
        MahmoudAlmalah\LaravelApiHelpers\Providers\LaravelApiHelpersServiceProvider::class,
        'api-helpers-lang',
    );

    expect($paths)->toHaveCount(1)
        ->and(str_replace('\\', '/', array_values($paths)[0]))->toEndWith('lang/vendor/api-helpers');
});
