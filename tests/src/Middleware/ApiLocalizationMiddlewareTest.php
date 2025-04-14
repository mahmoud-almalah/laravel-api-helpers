<?php

declare(strict_types=1);

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function (): void {
    Route::get('/api/localization-test', static fn () => response()->json([
        'locale' => App::getLocale(),
    ]))->middleware('api-localization');
});

it('sets the locale based on Accept-Language header', function (): void {
    Config::set('laravel-api-platform.localization.status', true);
    Config::set('laravel-api-platform.localization.locales', ['ar', 'en']);

    $response = $this->getJson('/api/localization-test', [
        'Accept-Language' => 'ar',
    ]);

    $response->assertStatus(Response::HTTP_OK);
    $response->assertJson([
        'locale' => 'ar',
    ]);
});

it('falls back to default locale if Accept-Language header is not set', function (): void {
    Config::set('laravel-api-platform.localization.status', true);
    Config::set('laravel-api-platform.localization.locales', ['ar', 'en']);
    Config::set('app.fallback_locale', 'en');

    $response = $this->getJson('/api/localization-test');

    $response->assertStatus(Response::HTTP_OK);
    $response->assertJson([
        'locale' => 'en',
    ]);
});
