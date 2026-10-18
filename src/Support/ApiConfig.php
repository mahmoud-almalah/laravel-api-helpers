<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Support;

use Throwable;

/**
 * Typed access to `config/api-helpers.php` that tolerates a missing or
 * partial container, so responses stay usable outside a booted application.
 *
 * @internal
 */
final class ApiConfig
{
    public static function string(string $key, string $default): string
    {
        $value = self::get($key);

        return is_string($value) ? $value : $default;
    }

    public static function bool(string $key, bool $default): bool
    {
        $value = self::get($key);

        return is_bool($value) ? $value : $default;
    }

    private static function get(string $key): mixed
    {
        try {
            return config($key);
        } catch (Throwable) {
            return null;
        }
    }
}
