<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * When {@see session()} has key {@code locale} set to a supported content code, overrides {@see app()->setLocale()}.
 */
class SetLocaleFromSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');
        if (! is_string($locale)) {
            return $next($request);
        }

        if (Language::isValidSessionUiLocale($locale)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
