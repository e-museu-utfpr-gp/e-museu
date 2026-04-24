<?php

declare(strict_types=1);

namespace App\Support\Admin\Ai;

/**
 * Immutable HTTP contract for {@see \App\Client\Ai\AiChatCompletionHttpClient}.
 * All endpoint-specific values come from configuration (typically backed by .env).
 * {@see $providerUrl} is the same value as `provider_url` in each `config('ai.{slug}')` block.
 */
final readonly class ChatCompletionHttpRequest
{
    /**
     * @param  array<string, string>  $extraHeaders  Merged with Authorization; keys are HTTP header names.
     */
    public function __construct(
        public string $providerUrl,
        public string $bearerToken,
        public array $extraHeaders,
        public int $connectTimeoutSeconds,
        public int $timeoutSeconds,
        public float $temperature,
        public int $maxTokens,
        public string $httpErrorPrefix,
        public string $humanLabel,
    ) {
    }
}
