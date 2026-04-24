<?php

declare(strict_types=1);

namespace App\Actions\Admin\Ai\AdminChatCompletion;

use App\Actions\Admin\Ai\AdminChatCompletion\Concerns\TriesAdminChatCompletionProviders;
use App\Actions\Admin\Ai\AdminChatCompletion\Concerns\TranslatesAdminContent;
use App\Client\Ai\AiChatCompletionHttpClient;
use App\Exceptions\AiTranslationUserException;
use App\Support\Admin\Ai\AdminAi;
use App\Support\Admin\Ai\AdminChatCompletionHttpRequestFactory;
use App\Support\Admin\Ai\ModelJsonContentDecoder;

/**
 * Admin chat completion (ordered chain from config) plus optional form translation via
 * {@see \App\Actions\Admin\Ai\AdminChatCompletion\Concerns\TranslatesAdminContent}.
 *
 * Provider tries and recoverable fallback:
 * {@see \App\Actions\Admin\Ai\AdminChatCompletion\Concerns\TriesAdminChatCompletionProviders}.
 * HTTP: {@see \App\Client\Ai\AiChatCompletionHttpClient}.
 */
final class AdminChatCompletionAction
{
    use TriesAdminChatCompletionProviders;
    use TranslatesAdminContent;

    public function __construct(
        private readonly AiChatCompletionHttpClient $httpClient,
        private readonly ModelJsonContentDecoder $modelJsonDecoder,
    ) {
    }

    /**
     * @param  list<string>  $modelsForPrimaryChainStep  Model list for the first entry in
     * {@see AdminAi::chatCompletionChainSlugs()}.
     * @return array{content: string, model: string, provider: string}
     *
     * @throws AiTranslationUserException
     */
    public function handle(
        string $systemPrompt,
        string $userPrompt,
        array $modelsForPrimaryChainStep,
        ?string $forcedProvider = null,
    ): array {
        if ($forcedProvider !== null) {
            return $this->handleForcedProvider(
                $forcedProvider,
                $systemPrompt,
                $userPrompt,
                $modelsForPrimaryChainStep,
            );
        }

        /** @var list<AiTranslationUserException> $recoverableChain */
        $recoverableChain = [];

        $first = AdminAi::firstChatCompletionChainSlug();
        foreach (AdminAi::chatCompletionChainSlugs() as $slug) {
            $models = ($slug === $first)
                ? $modelsForPrimaryChainStep
                : AdminAi::configuredModelsFor($slug);

            $result = $this->tryProviderChatCompletion(
                $slug,
                $systemPrompt,
                $userPrompt,
                $models,
                $recoverableChain,
            );
            if ($result !== null) {
                return $result;
            }
        }

        if ($recoverableChain !== []) {
            throw $this->exceptionAfterExhaustedRecoverableChain($recoverableChain);
        }

        if (AdminAi::anyAdminAiProviderSwitchEnabled()) {
            AdminAi::warnOnceIfProvidersRequestedButNoneReady();
        }

        throw new AiTranslationUserException('view.admin.ai.not_configured');
    }

    /**
     * @param  list<string>  $modelsForPrimaryChainStep
     * @return array{content: string, model: string, provider: string}
     */
    private function handleForcedProvider(
        string $forcedProvider,
        string $systemPrompt,
        string $userPrompt,
        array $modelsForPrimaryChainStep,
    ): array {
        if (! in_array($forcedProvider, AdminAi::allProviderSlugs(), true)) {
            throw new AiTranslationUserException('view.admin.ai.not_configured');
        }

        $first = AdminAi::firstChatCompletionChainSlug();
        $models = ($forcedProvider === $first)
            ? $modelsForPrimaryChainStep
            : AdminAi::configuredModelsFor($forcedProvider);

        $http = AdminChatCompletionHttpRequestFactory::fromProviderSlug($forcedProvider);
        if ($http === null) {
            throw new AiTranslationUserException('view.admin.ai.not_configured');
        }

        try {
            return [
                ...$this->httpClient->chatCompletion($http, $systemPrompt, $userPrompt, $models),
                'provider' => $forcedProvider,
            ];
        } catch (AiTranslationUserException $selectedProviderException) {
            throw new AiTranslationUserException('view.admin.ai.error_selected_provider_failed', [
                'provider' => AdminAi::providerLabel($forcedProvider),
            ], $selectedProviderException);
        }
    }
}
