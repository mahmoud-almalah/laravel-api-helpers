<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * @internal
 */
final class LaravelApiHelpersServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'api-helpers');

        $this->publishes([
            __DIR__.'/../../config/api-helpers.php' => config_path('api-helpers.php'),
        ], 'api-helpers-config');

        $this->publishes([
            __DIR__.'/../../lang' => $this->app->langPath('vendor/api-helpers'),
        ], 'api-helpers-lang');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/api-helpers.php', 'api-helpers');
    }
}
