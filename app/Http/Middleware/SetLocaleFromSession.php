<?php

namespace App\Http\Middleware;

use App\Enums\Content\ContentLanguage;
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

        $allowed = [
            ContentLanguage::PT_BR->value,
            ContentLanguage::EN->value,
        ];
        if (in_array($locale, $allowed, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
