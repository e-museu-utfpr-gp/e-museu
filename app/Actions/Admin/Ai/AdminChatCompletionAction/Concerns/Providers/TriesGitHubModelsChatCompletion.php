<?php

declare(strict_types=1);

namespace App\Actions\Admin\Ai\AdminChatCompletionAction\Concerns\Providers;

use App\Exceptions\AiTranslationUserException;
use App\Support\Admin\Ai\AdminAi;

/**
 * Last tier: {@see \App\Client\Ai\GitHubModelsChatCompletionClient}.
 */
trait TriesGitHubModelsChatCompletion
{
    /**
     * @param  list<AiTranslationUserException>  $recoverableChain
     * @return array{content: string, model: string}|null
     *
     * @throws AiTranslationUserException
     */
    private function tryGitHubModels(
        string $systemPrompt,
        string $userPrompt,
        array &$recoverableChain,
    ): ?array {
        if (! AdminAi::githubModelsReady()) {
            return null;
        }

        /** @var list<string> $gitHubModels */
        $gitHubModels = config('ai.github_models.models', []);

        try {
            return $this->gitHubModels->chatCompletion($systemPrompt, $userPrompt, $gitHubModels);
        } catch (AiTranslationUserException $e) {
            return $this->handleRecoverableProviderFailure($e, null, 'github_models', $recoverableChain);
        }
    }
}
