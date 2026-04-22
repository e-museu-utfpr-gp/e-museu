<?php

declare(strict_types=1);

namespace App\Client\Ai;

use App\Exceptions\AiTranslationUserException;
use App\Support\Admin\Ai\OpenAiCompatibleChatClientSupport\OpenAiCompatibleChatClientSupport;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * GitHub Models inference API (OpenAI-style chat completions).
 *
 * @see https://docs.github.com/en/github-models/quickstart
 */
final class GitHubModelsChatCompletionClient
{
    private const HTTP_ERROR_PREFIX = 'GitHubModels';

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
            (float) config('ai.github_models.temperature'),
            (int) config('ai.github_models.max_tokens'),
            self::HTTP_ERROR_PREFIX,
            'GitHub Models',
        );
    }

    private function requireApiKey(): string
    {
        $apiKey = trim((string) config('ai.github_models.api_key'));
        if ($apiKey === '') {
            throw new AiTranslationUserException('view.admin.ai.not_configured');
        }

        return $apiKey;
    }

    private function pendingChatRequest(string $apiKey): PendingRequest
    {
        $timeout = (int) config('ai.github_models.timeout_seconds');
        $connectTimeout = (int) config('ai.github_models.connect_timeout_seconds');
        $accept = trim((string) config('ai.github_models.accept_header'));
        if ($accept === '') {
            $accept = 'application/vnd.github+json';
        }
        $apiVersion = trim((string) config('ai.github_models.api_version'));
        if ($apiVersion === '') {
            $apiVersion = '2022-11-28';
        }

        return Http::asJson()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept' => $accept,
                'X-GitHub-Api-Version' => $apiVersion,
            ])
            ->connectTimeout($connectTimeout)
            ->timeout($timeout);
    }

    private function chatCompletionsUrl(): string
    {
        $url = trim((string) config('ai.github_models.inference_url'));
        if ($url === '') {
            return 'https://models.github.ai/inference/chat/completions';
        }

        return rtrim($url, '/');
    }
}
