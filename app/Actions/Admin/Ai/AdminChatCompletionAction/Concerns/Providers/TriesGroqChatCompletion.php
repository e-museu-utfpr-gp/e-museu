<?php

declare(strict_types=1);

namespace App\Actions\Admin\Ai\AdminChatCompletionAction\Concerns\Providers;

use App\Exceptions\AiTranslationUserException;
use App\Support\Admin\Ai\AdminAi;

/**
 * Second tier: {@see \App\Client\Ai\GroqChatCompletionClient} (after OpenRouter on recoverable failures).
 */
trait TriesGroqChatCompletion
{
    /**
     * @param  list<AiTranslationUserException>  $recoverableChain
     * @return array{content: string, model: string}|null
     *
     * @throws AiTranslationUserException
     */
    private function tryGroq(
        string $systemPrompt,
        string $userPrompt,
        array &$recoverableChain,
    ): ?array {
        if (! AdminAi::groqReady()) {
            return null;
        }

        /** @var list<string> $groqModels */
        $groqModels = config('ai.groq.models', []);

        try {
            return $this->groq->chatCompletion($systemPrompt, $userPrompt, $groqModels);
        } catch (AiTranslationUserException $e) {
            return $this->handleRecoverableProviderFailure(
                $e,
                AdminAi::githubModelsReady() ? 'github_models' : null,
                'groq',
                $recoverableChain,
            );
        }
    }
}
