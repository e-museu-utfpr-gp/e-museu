<?php

declare(strict_types=1);

namespace App\Providers\Concerns;

use Illuminate\Support\Facades\URL;

final class RegistersForcedHttpsUrls
{
    public static function register(): void
    {
        if (! filter_var(config('app.force_https', false), FILTER_VALIDATE_BOOL)) {
            return;
        }

        URL::forceScheme('https');
        URL::forceRootUrl((string) config('app.url'));
    }
}
