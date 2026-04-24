<?php

declare(strict_types=1);

namespace App\Support\Admin\Ai;

/**
 * Builds {@see ChatCompletionHttpRequest} from a provider section under {@see config('ai')}.
 * Expects each section to define {@see provider_url} (full POST URL for chat completions).
 */
final class AdminChatCompletionHttpRequestFactory
{
    public static function fromProviderSlug(string $slug): ?ChatCompletionHttpRequest
    {
        if (! in_array($slug, AdminAi::allProviderSlugs(), true)) {
            return null;
        }

        /** @var array<string, mixed> $section */
        $section = config('ai.' . $slug, []);
        if ($section === []) {
            return null;
        }

        $credentials = self::extractApiKeyAndUrl($section);
        if ($credentials === null) {
            return null;
        }

        return self::buildRequest($credentials['url'], $credentials['key'], $section);
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array{key: string, url: string}|null
     */
    private static function extractApiKeyAndUrl(array $section): ?array
    {
        $apiKey = trim((string) ($section['api_key'] ?? ''));
        $providerUrl = trim((string) ($section['provider_url'] ?? ''));
        if ($apiKey === '' || $providerUrl === '') {
            return null;
        }

        return ['key' => $apiKey, 'url' => $providerUrl];
    }

    /**
     * @param  array<string, mixed>  $section
     */
    private static function buildRequest(string $providerUrl, string $apiKey, array $section): ChatCompletionHttpRequest
    {
        $headers = self::normalizeStringHeaders($section['extra_request_headers'] ?? []);

        $connect = (int) ($section['connect_timeout_seconds'] ?? 15);
        $timeout = (int) ($section['timeout_seconds'] ?? 90);
        $temperature = (float) ($section['temperature'] ?? 0.2);
        $maxTokens = (int) ($section['max_tokens'] ?? 2048);
        $httpPrefix = trim((string) ($section['http_error_prefix'] ?? ''));
        if ($httpPrefix === '') {
            $httpPrefix = 'ChatHttp';
        }
        $human = trim((string) ($section['human_label'] ?? ''));
        if ($human === '') {
            $human = 'Chat';
        }

        return new ChatCompletionHttpRequest(
            $providerUrl,
            $apiKey,
            $headers,
            max(2, $connect),
            max(5, $timeout),
            min(2.0, max(0.0, $temperature)),
            max(256, $maxTokens),
            $httpPrefix,
            $human,
        );
    }

    /**
     * @return array<string, string>
     */
    private static function normalizeStringHeaders(mixed $extra): array
    {
        if (! is_array($extra)) {
            return [];
        }

        /** @var array<string, string> $headers */
        $headers = [];
        foreach ($extra as $k => $v) {
            if (! is_string($k) || ! is_string($v)) {
                continue;
            }
            $k = trim($k);
            if ($k === '') {
                continue;
            }
            $headers[$k] = $v;
        }

        return $headers;
    }
}
