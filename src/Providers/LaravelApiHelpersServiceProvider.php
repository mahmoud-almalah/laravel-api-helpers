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
        $this->publishes([
            __DIR__.'/../../config/api-helpers.php' => config_path('api-helpers.php'),
        ], 'api-helpers-config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/api-helpers.php', 'api-helpers');
    }
}
