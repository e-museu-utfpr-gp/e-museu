<?php

declare(strict_types=1);

namespace App\Support\Admin\Ai\OpenAiCompatibleChatClientSupport\Concerns;

use App\Exceptions\AiTranslationUserException;

trait BuildsOpenAiCompatibleChatRequest
{
    /**
     * @param  list<string>  $models
     */
    private static function assertModelsNonEmpty(array $models): void
    {
        if ($models === []) {
            throw new AiTranslationUserException('view.admin.ai.error_no_models');
        }
    }

    /**
     * @return array<string, mixed>
     */
    private static function chatRequestBody(
        string $model,
        string $systemPrompt,
        string $userPrompt,
        float $temperature,
        int $maxTokens,
    ): array {
        return [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
        ];
    }
}
