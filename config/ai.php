<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Admin AI features
    |--------------------------------------------------------------------------
    |
    | Each provider has its own *_ENABLED flag. Admin AI is on only when at least
    | one enabled provider also has a non-empty API key and model list. If every
    | flag is false, translation assist is off. If any flag is true but no provider
    | is fully configured, a warning is logged and the UI behaves as unavailable.
    | Chat completions try OpenRouter, then Groq, then GitHub Models (each if enabled
    | and configured); recoverable failures fall through to the next provider.
    |
    */
    'rate_limit' => [
        'per_minute' => max(1, (int) env('AI_RATE_LIMIT_PER_MINUTE', 3)),
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenRouter (first in the admin chat completion chain)
    |--------------------------------------------------------------------------
    */
    'openrouter' => [
        'enabled' => filter_var(env('OPENROUTER_ENABLED', true), FILTER_VALIDATE_BOOL),
        'api_key' => env('OPENROUTER_API_KEY', ''),
        'base_url' => rtrim((string) env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'), '/'),
        'timeout_seconds' => max(5, (int) env('OPENROUTER_TIMEOUT', 90)),
        'connect_timeout_seconds' => max(2, (int) env('OPENROUTER_CONNECT_TIMEOUT', 15)),
        /**
         * Comma-separated model ids, tried in order until one succeeds (HTTP + JSON parse).
         * Free-tier examples change over time; override via OPENROUTER_MODELS in .env.
         */
        /** Default starts with `openrouter/free` (OpenRouter pooled free tier). Override in .env if needed. */
        'models' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env(
                'OPENROUTER_MODELS',
                'openrouter/free,meta-llama/llama-3.2-3b-instruct:free'
            ))
        ))),
        'temperature' => min(2.0, max(0.0, (float) env('OPENROUTER_TEMPERATURE', 0.2))),
        /** Free models often reject very high max_tokens; keep default conservative. */
        'max_tokens' => max(256, (int) env('OPENROUTER_MAX_TOKENS', 2048)),
    ],

    /*
    |--------------------------------------------------------------------------
    | Groq (second; after OpenRouter, before GitHub Models)
    |--------------------------------------------------------------------------
    |
    | Optional. When enabled with GROQ_API_KEY, used after OpenRouter on recoverable
    | failures, then GitHub Models if configured. Same chat-completions shape as OpenAI.
    |
    */
    'groq' => [
        'enabled' => filter_var(env('GROQ_ENABLED', true), FILTER_VALIDATE_BOOL),
        'api_key' => env('GROQ_API_KEY', ''),
        'base_url' => rtrim((string) env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'), '/'),
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
    ],

    /*
    |--------------------------------------------------------------------------
    | GitHub Models (last fallback after OpenRouter and Groq)
    |--------------------------------------------------------------------------
    |
    | Personal access token needs the "models" scope. Inference URL and API
    | version follow GitHub docs: https://docs.github.com/en/github-models/quickstart
    |
    */
    'github_models' => [
        'enabled' => filter_var(env('GITHUB_MODELS_ENABLED', true), FILTER_VALIDATE_BOOL),
        'api_key' => env('GITHUB_MODELS_TOKEN', ''),
        'inference_url' => trim((string) env(
            'GITHUB_MODELS_INFERENCE_URL',
            'https://models.github.ai/inference/chat/completions'
        )),
        'api_version' => trim((string) env('GITHUB_MODELS_API_VERSION', '2022-11-28')),
        'accept_header' => trim((string) env('GITHUB_MODELS_ACCEPT', 'application/vnd.github+json')),
        'timeout_seconds' => max(5, (int) env('GITHUB_MODELS_TIMEOUT', 90)),
        'connect_timeout_seconds' => max(2, (int) env('GITHUB_MODELS_CONNECT_TIMEOUT', 15)),
        /**
         * Comma-separated model ids, tried in order until one succeeds (same as OpenRouter / Groq).
         * Default fallback uses another publisher (Meta) after OpenAI; ids must exist in the catalog.
         *
         * @see https://models.github.ai/catalog/models
         */
        'models' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env(
                'GITHUB_MODELS',
                'openai/gpt-4o-mini,meta/meta-llama-3.1-8b-instruct'
            ))
        ))),
        'temperature' => min(2.0, max(0.0, (float) env('GITHUB_MODELS_TEMPERATURE', 0.2))),
        'max_tokens' => max(256, (int) env('GITHUB_MODELS_MAX_TOKENS', 2048)),
    ],

    'translation' => [
        /** Total characters of source snippets sent to the model (soft guard). */
        'max_source_chars' => max(1000, (int) env('AI_TRANSLATION_MAX_SOURCE_CHARS', 120_000)),
    ],
];
