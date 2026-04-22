<?php

declare(strict_types=1);

namespace App\Client\Ai;

use App\Exceptions\AiTranslationUserException;
use App\Support\Admin\Ai\OpenAiCompatibleChatClientSupport\OpenAiCompatibleChatClientSupport;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

final class OpenRouterChatCompletionClient
{
    private const HTTP_ERROR_PREFIX = 'OpenRouter';

    /**
     * @param  list<string>  $models
     * @return array{content: string, model: string}
     *
     * @throws AiTranslationUserException When every model fails or the response is unusable.
     */
    public function chatCompletion(string $systemPrompt, string $userPrompt, array $models): array
    {
        $apiKey = $this->requireApiKey();

        return OpenAiCompatibleChatClientSupport::completeAcrossModels(
            $this->pendingChatRequest($apiKey),
            $this->chatCompletionsUrl(),
            $systemPrompt,
            $userPrompt,
            $models,
            (float) config('ai.openrouter.temperature'),
            (int) config('ai.openrouter.max_tokens'),
            self::HTTP_ERROR_PREFIX,
            'OpenRouter',
        );
    }

    private function requireApiKey(): string
    {
        $apiKey = trim((string) config('ai.openrouter.api_key'));
        if ($apiKey === '') {
            throw new AiTranslationUserException('view.admin.ai.not_configured');
        }

        return $apiKey;
    }

    private function pendingChatRequest(string $apiKey): PendingRequest
    {
        $timeout = (int) config('ai.openrouter.timeout_seconds');
        $connectTimeout = (int) config('ai.openrouter.connect_timeout_seconds');
        $referer = rtrim((string) config('app.url'), '/');
        $title = (string) config('app.name');

        return Http::asJson()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'HTTP-Referer' => $referer !== '' ? $referer : 'http://localhost',
                'X-OpenRouter-Title' => $title !== '' ? $title : 'e-museu',
            ])
            ->connectTimeout($connectTimeout)
            ->timeout($timeout);
    }

    private function chatCompletionsUrl(): string
    {
        $raw = rtrim((string) config('ai.openrouter.base_url'), '/');
        if ($raw === '') {
            $raw = 'https://openrouter.ai/api/v1';
        }
        if (str_ends_with($raw, '/v1')) {
            return $raw . '/chat/completions';
        }
        if (str_ends_with($raw, '/api')) {
            return $raw . '/v1/chat/completions';
        }

        return $raw . '/api/v1/chat/completions';
    }
}
