<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Admin AI features
    |--------------------------------------------------------------------------
    |
    | Each provider block is one HTTP endpoint: full chat completions URL in provider_url,
    | API key, timeouts, models, optional extra headers. Order for Auto mode:
    | AI_CHAT_COMPLETION_CHAIN (comma-separated keys matching sections below).
    | human_label (from *_LOG_LABEL env vars) is the name shown in the admin translation UI for that block.
    |
    */
    'rate_limit' => [
        'per_minute' => max(1, (int) env('AI_RATE_LIMIT_PER_MINUTE', 3)),
    ],

    'chat_completion' => [
        'chain' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env(
                'AI_CHAT_COMPLETION_CHAIN',
                'groq,openrouter,github_models'
            ))
        ))),
    ],

    'groq' => [
        'enabled' => filter_var(env('GROQ_ENABLED', false), FILTER_VALIDATE_BOOL),
        'api_key' => env('GROQ_API_KEY', ''),
        'provider_url' => trim((string) env(
            'GROQ_PROVIDER_URL',
            'https://api.groq.com/openai/v1/chat/completions'
        )),
        'timeout_seconds' => max(5, (int) env('GROQ_TIMEOUT', 90)),
        'connect_timeout_seconds' => max(2, (int) env('GROQ_CONNECT_TIMEOUT', 15)),
        'models' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env(
                'GROQ_MODELS',
                'llama-3.1-8b-instant,gemma2-9b-it'
            ))
        ))),
        'temperature' => min(2.0, max(0.0, (float) env('GROQ_TEMPERATURE', 0.2))),
        'max_tokens' => max(256, (int) env('GROQ_MAX_TOKENS', 2048)),
        'http_error_prefix' => (string) env('GROQ_HTTP_ERROR_PREFIX', 'Groq'),
        'human_label' => (string) env('GROQ_LOG_LABEL', 'Groq'),
        'extra_request_headers' => [],
    ],

    'openrouter' => [
        'enabled' => filter_var(env('OPENROUTER_ENABLED', false), FILTER_VALIDATE_BOOL),
        'api_key' => env('OPENROUTER_API_KEY', ''),
        'provider_url' => trim((string) env(
            'OPENROUTER_PROVIDER_URL',
            'https://openrouter.ai/api/v1/chat/completions'
        )),
        'timeout_seconds' => max(5, (int) env('OPENROUTER_TIMEOUT', 90)),
        'connect_timeout_seconds' => max(2, (int) env('OPENROUTER_CONNECT_TIMEOUT', 15)),
        'models' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env(
                'OPENROUTER_MODELS',
                'openrouter/free,meta-llama/llama-3.2-3b-instruct:free'
            ))
        ))),
        'temperature' => min(2.0, max(0.0, (float) env('OPENROUTER_TEMPERATURE', 0.2))),
        'max_tokens' => max(256, (int) env('OPENROUTER_MAX_TOKENS', 2048)),
        'http_error_prefix' => (string) env('OPENROUTER_HTTP_ERROR_PREFIX', 'OpenRouter'),
        'human_label' => (string) env('OPENROUTER_LOG_LABEL', 'OpenRouter'),
        'extra_request_headers' => [
            'HTTP-Referer' => rtrim((string) env('OPENROUTER_HTTP_REFERER', env('APP_URL', 'http://localhost')), '/')
                ?: 'http://localhost',
            'X-OpenRouter-Title' => trim((string) env('OPENROUTER_HTTP_TITLE', env('APP_NAME', 'e-museu')))
                ?: 'e-museu',
        ],
    ],

    'github_models' => [
        'enabled' => filter_var(env('GITHUB_MODELS_ENABLED', false), FILTER_VALIDATE_BOOL),
        'api_key' => env('GITHUB_MODELS_TOKEN', ''),
        'provider_url' => trim((string) env(
            'GITHUB_MODELS_PROVIDER_URL',
            'https://models.github.ai/inference/chat/completions'
        )),
        'timeout_seconds' => max(5, (int) env('GITHUB_MODELS_TIMEOUT', 90)),
        'connect_timeout_seconds' => max(2, (int) env('GITHUB_MODELS_CONNECT_TIMEOUT', 15)),
        'models' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env(
                'GITHUB_MODELS',
                'openai/gpt-4o-mini,meta/meta-llama-3.1-8b-instruct'
            ))
        ))),
        'temperature' => min(2.0, max(0.0, (float) env('GITHUB_MODELS_TEMPERATURE', 0.2))),
        'max_tokens' => max(256, (int) env('GITHUB_MODELS_MAX_TOKENS', 2048)),
        'http_error_prefix' => (string) env('GITHUB_MODELS_HTTP_ERROR_PREFIX', 'GitHubModels'),
        'human_label' => (string) env('GITHUB_MODELS_LOG_LABEL', 'GitHub Models'),
        'extra_request_headers' => [
            'Accept' => trim((string) env('GITHUB_MODELS_ACCEPT', 'application/vnd.github+json'))
                ?: 'application/vnd.github+json',
            'X-GitHub-Api-Version' => trim((string) env('GITHUB_MODELS_API_VERSION', '2022-11-28')) ?: '2022-11-28',
        ],
    ],

    'translation' => [
        // Enforced with strlen() on the UTF-8 source document (byte count), not mb_strlen().
        'max_source_chars' => max(1000, (int) env('AI_TRANSLATION_MAX_SOURCE_CHARS', 120_000)),
    ],
];
