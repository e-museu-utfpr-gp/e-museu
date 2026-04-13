<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Anti-bot challenge driver
    |--------------------------------------------------------------------------
    |
    | "null" disables server-side checks and hides client widgets (tests, local).
    | "turnstile" uses Cloudflare Turnstile (see https://developers.cloudflare.com/turnstile/).
    | When active, middleware `antibot` protects `POST /catalog/collaborators/request-verification-code`
    | (scope `verification-request`, AJAX e-mail code) and `POST admin/auth/login` (default field). See `routes/web.php`.
    | To support another provider, replace {@see \App\Support\Security\AntiBotVerifier} or bind a custom implementation in a service provider.
    |
    */

    'driver' => env('ANTIBOT_DRIVER', 'null'),

    /*
    |--------------------------------------------------------------------------
    | POST field carrying the provider token
    |--------------------------------------------------------------------------
    |
    | Cloudflare Turnstile default: cf-turnstile-response.
    | Other providers (e.g. hCaptcha) use a different name — set ANTIBOT_RESPONSE_INPUT when
    | you swap the driver implementation.
    |
    */

    'response_input' => env('ANTIBOT_RESPONSE_INPUT', 'cf-turnstile-response'),

    /*
    |--------------------------------------------------------------------------
    | Turnstile token field — “send verification e-mail” (AJAX)
    |--------------------------------------------------------------------------
    |
    | Used only by `POST /catalog/collaborators/request-verification-code` (middleware `antibot:verification-request`).
    | The widget is rendered in `email-verification-code` Blade: wrapper `data-response-field`, inner `data-response-field-name`.
    | If you ever add a second Turnstile on the same HTML form as the admin-login widget, keep this name distinct from
    | `response_input` so the two POST fields do not collide.
    |
    */

    'verification_request_response_input' => env(
        'ANTIBOT_VERIFICATION_REQUEST_RESPONSE_INPUT',
        'cf_turnstile_response_request_verify',
    ),

    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
        'verify_url' => env('TURNSTILE_VERIFY_URL', 'https://challenges.cloudflare.com/turnstile/v0/siteverify'),
    ],

];
