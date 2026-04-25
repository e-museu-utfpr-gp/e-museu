<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Public catalog collaborator e-mail verification
    |--------------------------------------------------------------------------
    |
    | When disabled, public item/extra contribution does not require the
    | request/confirm verification-code flow and related UI can stay hidden.
    |
    */

    'public_contribution_email_verification_enabled' => (bool) env(
        'MAIL_PUBLIC_CONTRIBUTION_EMAIL_VERIFICATION_ENABLED',
        false,
    ),

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Primary outbound path is SMTP (e.g. UTFPR). Tests set MAIL_MAILER=array in phpunit.xml.
    |
    */

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME'),
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', 'smtp.utfpr.edu.br'),
            'port' => env('MAIL_PORT', 587),
            'username' => env('MAIL_USERNAME', 'e-museu-gp@utfpr.edu.br'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
            'retry_after' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Outgoing mail readiness (optional credentials check)
    |--------------------------------------------------------------------------
    |
    | Keys must match mail.mailers.*.transport. Values are config dot paths that
    | must be non-empty before the app treats the driver as configured. Transports
    | not listed here are treated as not configured until you add an entry.
    |
    */

    'transport_required_config' => [
        'postmark' => 'services.postmark.token',
        'mailgun' => 'services.mailgun.secret',
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'e-museu-gp@utfpr.edu.br'),
        'name' => env('MAIL_FROM_NAME', env('APP_NAME', 'Laravel E-Museu')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    */

    'markdown' => [
        'theme' => env('MAIL_MARKDOWN_THEME', 'default'),

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

];
