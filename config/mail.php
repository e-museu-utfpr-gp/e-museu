<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    */

    'default' => env('MAIL_MAILER', 'resend'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Outgoing mail uses Resend only. Tests set MAIL_MAILER=array in phpunit.xml.
    |
    */

    'mailers' => [
        'resend' => [
            'transport' => 'resend',
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
                'resend',
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
        'resend' => 'services.resend.key',
        'postmark' => 'services.postmark.token',
        'mailgun' => 'services.mailgun.secret',
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
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
