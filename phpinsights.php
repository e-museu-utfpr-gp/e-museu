<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Preset
    |--------------------------------------------------------------------------
    |
    | This preset configures PHP Insights to analyze code using Laravel
    | conventions and best practices.
    |
    */

    'preset' => 'laravel',

    /*
    |--------------------------------------------------------------------------
    | Exclude
    |--------------------------------------------------------------------------
    |
    | Here you may specify which directories should be excluded from analysis.
    |
    */

    'exclude' => [
        'bootstrap/cache',
        'storage',
        'vendor',
        'node_modules',
        'public',
        'database/migrations',
    ],

    /*
    |--------------------------------------------------------------------------
    | Add
    |--------------------------------------------------------------------------
    |
    | Here you may add any additional insights to be used during analysis.
    |
    */

    'add' => [
        //  Example\Class::class => [
        //      ExampleRule::class,
        //  ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Remove
    |--------------------------------------------------------------------------
    |
    | Here you may remove any insights that you don't want to use during
    | analysis.
    |
    */

    'remove' => [
        //  Example\Class::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Config
    |--------------------------------------------------------------------------
    |
    | Here you may adjust the configuration of specific insights.
    |
    */

    'config' => [
        //  Example\Class::class => [
        //      'key' => 'value',
        //  ],
    ],

];
