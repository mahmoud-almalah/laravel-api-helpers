<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Support;

use Throwable;

/**
 * Typed access to the package config and translated messages that tolerates a
 * missing or partial container, so responses stay usable outside a booted application.
 *
 * @internal
 */
final class ApiConfig
{
    private const string TRANSLATION_NAMESPACE = 'api-helpers::messages.';

    /**
     * @var array<string, string>|null
     */
    private static ?array $english = null;

    /**
     * A user-facing message from `lang/{locale}/messages.php`.
     *
     * A non-empty string config value ($overrideKey) wins over the translation,
     * except the old built-in English default, which older published configs
     * still contain and which must not stop translation.
     */
    public static function message(string $name, ?string $overrideKey = null): string
    {
        $override = $overrideKey === null ? null : self::get($overrideKey);

        if (is_string($override) && $override !== '' && $override !== (self::english()[$name] ?? null)) {
            return $override;
        }

        try {
            $translated = __(self::TRANSLATION_NAMESPACE.$name);
        } catch (Throwable) {
            $translated = null;
        }

        if (is_string($translated) && $translated !== self::TRANSLATION_NAMESPACE.$name) {
            return $translated;
        }

        return self::english()[$name] ?? $name;
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

    /**
     * @return array<string, string>
     */
    private static function english(): array
    {
        if (self::$english === null) {
            /** @var array<string, string> $messages */
            $messages = require __DIR__.'/../../lang/en/messages.php';

            self::$english = $messages;
        }

        return self::$english;
    }
}
