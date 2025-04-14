<?php

declare(strict_types=1);

namespace MahmoudAlmalah\LaravelApiHelpers\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Number;

final class ApiLocalizationMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->isJson() && $request->hasHeader('Accept-Language')) {
            $language = mb_strtolower((string) $request->header('Accept-Language')); // @phpstan-ignore-line

            if (in_array($language, Config::array('laravel-api-platform.localization.locales'), true)) {
                app()->setLocale($language);
                Number::useLocale($language);
            }
        }

        return $next($request);
    }
}
