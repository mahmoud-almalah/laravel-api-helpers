<?php

declare(strict_types=1);

namespace Tests;

use MahmoudAlmalah\LaravelApiHelpers\Providers\LaravelApiHelpersServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Load the service provider.
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelApiHelpersServiceProvider::class,
        ];
    }
}
