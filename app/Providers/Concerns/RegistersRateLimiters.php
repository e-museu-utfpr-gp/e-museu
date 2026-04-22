<?php

declare(strict_types=1);

namespace App\Providers\Concerns;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

final class RegistersRateLimiters
{
    public static function register(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('web-public', function (Request $request) {
            return Limit::perMinute(90)->by($request->ip());
        });

        /** Generous limit for catalog autocomplete / name-check JSON (shared IPs, many quick requests). */
        RateLimiter::for('web-catalog-light', function (Request $request) {
            return Limit::perMinute(300)->by($request->ip());
        });

        /**
         * Stricter cap for public item contribution POST (`catalog.items.store`), in addition to `web-public`.
         * Mitigates automated submissions without affecting lighter catalog JSON endpoints.
         */
        RateLimiter::for('catalog-item-contribution-store', function (Request $request) {
            return Limit::perMinute(15)->by($request->ip());
        });

        RateLimiter::for('web-admin', function (Request $request) {
            return Limit::perMinute(480)->by($request->user()?->id ?: $request->ip());
        });

        /** Admin OpenRouter translation assist — strict per-user cap (see config/ai.php). */
        RateLimiter::for('admin-ai-translate', function (Request $request) {
            $perMinute = max(1, (int) config('ai.rate_limit.per_minute', 8));

            return Limit::perMinute($perMinute)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('web-storage', function (Request $request) {
            return Limit::perMinute(1200)->by($request->ip());
        });

        /** Admin login: per IP + per normalised username (same bucket idea as collaborator verification e-mail). */
        RateLimiter::for('admin-login', function (Request $request) {
            $userKey = self::hashedNormalizedInputKey($request, 'username', 'no-username');

            return Limit::perMinute(5)->by($userKey . '|' . $request->ip());
        });

        /** Public catalog: verification code e-mail — per IP + per normalised e-mail (NAT-friendly split). */
        RateLimiter::for('collaborator-verification-email', function (Request $request) {
            $emailKey = self::hashedNormalizedInputKey($request, 'email', 'no-email');

            return Limit::perMinute(3)->by($emailKey . '|' . $request->ip());
        });

        /** Confirm code: per IP and per target e-mail to slow guessing across many IPs. */
        RateLimiter::for('collaborator-verification-confirm', function (Request $request) {
            $emailKey = self::hashedNormalizedInputKey($request, 'email', 'no-email');

            return [
                Limit::perMinute(12)->by($request->ip()),
                Limit::perMinute(8)->by('verify-confirm-email:' . $emailKey),
            ];
        });

        /** POST clear contribution session — light cap to limit noise. */
        RateLimiter::for('collaborator-clear-session', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        /** Public catalog: check-contact JSON while typing — per IP + per normalised e-mail. */
        RateLimiter::for('collaborator-check-contact', function (Request $request) {
            $emailKey = self::hashedNormalizedInputKey($request, 'email', 'no-email');

            return Limit::perMinute(120)->by($emailKey . '|' . $request->ip());
        });
    }

    /** Lowercase + trim request input, then sha256 for rate-limit keys; empty uses $emptyLabel. */
    private static function hashedNormalizedInputKey(Request $request, string $inputKey, string $emptyLabel): string
    {
        $value = mb_strtolower(trim((string) $request->input($inputKey, '')));

        return $value !== '' ? hash('sha256', $value) : $emptyLabel;
    }
}
