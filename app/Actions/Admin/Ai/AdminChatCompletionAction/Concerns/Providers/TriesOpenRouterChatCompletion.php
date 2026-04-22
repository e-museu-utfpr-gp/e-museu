<?php

declare(strict_types=1);

namespace App\Actions\Admin\Ai\AdminChatCompletionAction\Concerns\Providers;

use App\Exceptions\AiTranslationUserException;
use App\Support\Admin\Ai\AdminAi;

/**
 * First tier of the admin chat completion chain: {@see \App\Client\Ai\OpenRouterChatCompletionClient}.
 *
 * To remove or replace OpenRouter, adjust this concern and the try-order in
 * {@see \App\Actions\Admin\Ai\AdminChatCompletionAction\AdminChatCompletionAction::handle()}.
 */
trait TriesOpenRouterChatCompletion
{
    /**
     * @param  list<string>  $openRouterModels
     * @param  list<AiTranslationUserException>  $recoverableChain
     * @return array{content: string, model: string}|null
     *
     * @throws AiTranslationUserException
     */
    private function tryOpenRouter(
        string $systemPrompt,
        string $userPrompt,
        array $openRouterModels,
        array &$recoverableChain,
    ): ?array {
        if (! AdminAi::openRouterReady()) {
            return null;
        }

        try {
            return $this->openRouter->chatCompletion($systemPrompt, $userPrompt, $openRouterModels);
        } catch (AiTranslationUserException $e) {
            return $this->handleRecoverableProviderFailure(
                $e,
                $this->nextProviderAfterOpenRouter(),
                'openrouter',
                $recoverableChain,
            );
        }
    }

    /** @return 'groq'|'github_models'|null */
    private function nextProviderAfterOpenRouter(): ?string
    {
        if (AdminAi::groqReady()) {
            return 'groq';
        }

        return AdminAi::githubModelsReady() ? 'github_models' : null;
    }
}
