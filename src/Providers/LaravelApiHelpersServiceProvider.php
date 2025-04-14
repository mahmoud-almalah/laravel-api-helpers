<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use MahmoudAlmalah\LaravelApiHelpers\Middleware\ApiLocalizationMiddleware;

/**
 * @internal
 */
final class LaravelApiHelpersServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/laravel-api-platform.php' => config_path('laravel-api-platform.php'),
        ], 'laravel-api-platform-config');

        $this->publishes([
            __DIR__.'/../Middleware' => app_path('Http/Middleware'),
        ], 'laravel-api-platform-middleware');

        if (Config::boolean('laravel-api-platform.localization.status')) {
            /** @var Router $router */
            $router = $this->app->make('router');
            $router->aliasMiddleware('api-localization', ApiLocalizationMiddleware::class);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/laravel-api-platform.php', 'laravel-api-platform');
    }
}
