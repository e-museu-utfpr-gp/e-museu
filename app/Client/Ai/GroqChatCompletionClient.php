<?php

declare(strict_types=1);

namespace App\Client\Ai;

use App\Exceptions\AiTranslationUserException;
use App\Support\Admin\Ai\OpenAiCompatibleChatClientSupport\OpenAiCompatibleChatClientSupport;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Groq OpenAI-compatible chat completions (https://console.groq.com/docs/quickstart).
 */
final class GroqChatCompletionClient
{
    private const HTTP_ERROR_PREFIX = 'Groq';

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
            (float) config('ai.groq.temperature'),
            (int) config('ai.groq.max_tokens'),
            self::HTTP_ERROR_PREFIX,
            'Groq',
        );
    }

    private function requireApiKey(): string
    {
        $apiKey = trim((string) config('ai.groq.api_key'));
        if ($apiKey === '') {
            throw new AiTranslationUserException('view.admin.ai.not_configured');
        }

        return $apiKey;
    }

    private function pendingChatRequest(string $apiKey): PendingRequest
    {
        $timeout = (int) config('ai.groq.timeout_seconds');
        $connectTimeout = (int) config('ai.groq.connect_timeout_seconds');

        return Http::asJson()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])
            ->connectTimeout($connectTimeout)
            ->timeout($timeout);
    }

    private function chatCompletionsUrl(): string
    {
        $raw = rtrim((string) config('ai.groq.base_url'), '/');
        if ($raw === '') {
            $raw = 'https://api.groq.com/openai/v1';
        }

        return $raw . '/chat/completions';
    }
}
