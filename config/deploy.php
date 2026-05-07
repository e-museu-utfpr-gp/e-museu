<?php

declare(strict_types=1);

return [
    'secret' => env('DEPLOY_HOOK_SECRET'),
    'coolify' => [
        'deploy_url' => env('COOLIFY_DEPLOY_URL'),
        'token' => env('COOLIFY_DEPLOY_TOKEN'),
    ],
];
