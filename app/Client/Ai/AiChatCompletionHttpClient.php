<?php

declare(strict_types=1);

namespace App\Client\Ai;

use App\Support\Admin\Ai\ChatCompletionHttpRequest;
use App\Support\Admin\Ai\OpenAiCompatibleChatClientSupport\OpenAiCompatibleChatClientSupport;
use Illuminate\Support\Facades\Http;

/**
 * Stateless OpenAI-style chat HTTP caller: URL, bearer token, headers, and tuning
 * must be supplied via {@see ChatCompletionHttpRequest} (usually from application config / .env).
 */
final class AiChatCompletionHttpClient
{
    /**
     * @param  list<string>  $models
     * @return array{content: string, model: string}
     */
    public function chatCompletion(
        ChatCompletionHttpRequest $http,
        string $systemPrompt,
        string $userPrompt,
        array $models,
    ): array {
        $pending = Http::asJson()
            ->withHeaders(array_merge(
                ['Authorization' => 'Bearer ' . $http->bearerToken],
                $http->extraHeaders,
            ))
            ->connectTimeout($http->connectTimeoutSeconds)
            ->timeout($http->timeoutSeconds);

        return OpenAiCompatibleChatClientSupport::completeAcrossModels(
            $pending,
            $http->providerUrl,
            $systemPrompt,
            $userPrompt,
            $models,
            $http->temperature,
            $http->maxTokens,
            $http->httpErrorPrefix,
            $http->humanLabel,
        );
    }
}
