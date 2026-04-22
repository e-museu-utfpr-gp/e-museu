<?php

declare(strict_types=1);

namespace App\Actions\Admin\Ai\AdminChatCompletionAction;

use App\Actions\Admin\Ai\AdminChatCompletionAction\Concerns\Fallback\HandlesRecoverableChatCompletionFallback;
use App\Actions\Admin\Ai\AdminChatCompletionAction\Concerns\Providers\{
    TriesOpenRouterChatCompletion,
    TriesGroqChatCompletion,
    TriesGitHubModelsChatCompletion,
};
use App\Actions\Admin\Ai\AdminChatCompletionAction\Concerns\Translation\TranslatesAdminContent;
use App\Client\Ai\GitHubModelsChatCompletionClient;
use App\Client\Ai\GroqChatCompletionClient;
use App\Client\Ai\OpenRouterChatCompletionClient;
use App\Exceptions\AiTranslationUserException;
use App\Support\Admin\Ai\AdminAi;
use App\Support\Admin\Ai\ModelJsonContentDecoder;

/**
 * Admin chat completion (OpenRouter → Groq → GitHub) plus optional form translation via
 * {@see \App\Actions\Admin\Ai\AdminChatCompletionAction\Concerns\Translation\TranslatesAdminContent}.
 *
 * Provider tries: {@see \App\Actions\Admin\Ai\AdminChatCompletionAction\Concerns\Providers};
 * fallback: {@see \App\Actions\Admin\Ai\AdminChatCompletionAction\Concerns\Fallback}.
 */
final class AdminChatCompletionAction
{
    use HandlesRecoverableChatCompletionFallback;
    use TriesOpenRouterChatCompletion;
    use TriesGroqChatCompletion;
    use TriesGitHubModelsChatCompletion;
    use TranslatesAdminContent;

    public function __construct(
        private readonly OpenRouterChatCompletionClient $openRouter,
        private readonly GroqChatCompletionClient $groq,
        private readonly GitHubModelsChatCompletionClient $gitHubModels,
        private readonly ModelJsonContentDecoder $modelJsonDecoder,
    ) {
    }

    /**
     * @param  list<string>  $openRouterModels
     * @return array{content: string, model: string}
     *
     * @throws AiTranslationUserException
     */
    public function handle(string $systemPrompt, string $userPrompt, array $openRouterModels): array
    {
        /** @var list<AiTranslationUserException> $recoverableChain */
        $recoverableChain = [];

        $result = $this->tryOpenRouter($systemPrompt, $userPrompt, $openRouterModels, $recoverableChain);
        if ($result !== null) {
            return $result;
        }

        $result = $this->tryGroq($systemPrompt, $userPrompt, $recoverableChain);
        if ($result !== null) {
            return $result;
        }

        $result = $this->tryGitHubModels($systemPrompt, $userPrompt, $recoverableChain);
        if ($result !== null) {
            return $result;
        }

        if ($recoverableChain !== []) {
            throw $this->exceptionAfterExhaustedRecoverableChain($recoverableChain);
        }

        if (AdminAi::anyAdminAiProviderSwitchEnabled()) {
            AdminAi::warnOnceIfProvidersRequestedButNoneReady();
        }

        throw new AiTranslationUserException('view.admin.ai.not_configured');
    }
}
