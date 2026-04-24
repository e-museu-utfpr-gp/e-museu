<?php

declare(strict_types=1);

namespace App\Actions\Admin\Ai\AdminChatCompletion\Concerns;

use App\Exceptions\AiTranslationUserException;
use App\Support\Admin\Ai\AdminAi;
use App\Support\Admin\Ai\AdminChatCompletionHttpRequestFactory;
use Illuminate\Support\Facades\Log;

/**
 * Shared try-provider, recoverable-fallback logging, and final exception shaping for the admin
 * chat completion chain.
 *
 * Call order is owned by {@see \App\Actions\Admin\Ai\AdminChatCompletion\AdminChatCompletionAction::handle()}.
 *
 * @see \App\Client\Ai\AiChatCompletionHttpClient
 */
trait TriesAdminChatCompletionProviders
{
    /**
     * @param  list<string>  $models
     * @param  list<AiTranslationUserException>  $recoverableChain
     * @return array{content: string, model: string, provider: string}|null
     *
     * @throws AiTranslationUserException
     */
    private function tryProviderChatCompletion(
        string $providerSlug,
        string $systemPrompt,
        string $userPrompt,
        array $models,
        array &$recoverableChain,
    ): ?array {
        if (! $this->providerChainIsReady($providerSlug)) {
            return null;
        }

        $knownSlug = $providerSlug;

        $next = $this->nextProviderSlugAfter($knownSlug);

        $http = AdminChatCompletionHttpRequestFactory::fromProviderSlug($knownSlug);
        if ($http === null) {
            Log::warning('admin.ai.chat_completion_factory_null', [
                'message' => 'ChatCompletionHttpRequest factory returned null for a slug marked ready in the chain.',
                'provider_slug' => $knownSlug,
            ]);

            return null;
        }

        try {
            $result = $this->httpClient->chatCompletion(
                $http,
                $systemPrompt,
                $userPrompt,
                $models,
            );

            return [...$result, 'provider' => $knownSlug];
        } catch (AiTranslationUserException $translationFailure) {
            $this->handleRecoverableProviderFailure(
                $translationFailure,
                $next,
                $knownSlug,
                $recoverableChain,
            );

            return null;
        }
    }

    private function providerChainIsReady(string $providerSlug): bool
    {
        if (! in_array($providerSlug, AdminAi::chatCompletionChainSlugs(), true)) {
            return false;
        }

        return AdminAi::providerReady($providerSlug);
    }

    /**
     * Next slug in {@see AdminAi::chatCompletionChainSlugs()} after $from, when that slug is ready.
     */
    private function nextProviderSlugAfter(string $from): ?string
    {
        $chain = AdminAi::chatCompletionChainSlugs();
        $idx = array_search($from, $chain, true);
        if ($idx === false) {
            return null;
        }

        $len = count($chain);
        for ($j = (int) $idx + 1; $j < $len; $j++) {
            $candidate = $chain[$j];
            if (AdminAi::providerReady($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * @param  string|null  $next  Next slug in the configured chain, or null at end.
     * @param  list<AiTranslationUserException>  $recoverableChain
     * @return array{content: string, model: string}|null  null when caller should try the next tier
     *
     * @throws AiTranslationUserException
     */
    private function handleRecoverableProviderFailure(
        AiTranslationUserException $e,
        ?string $next,
        string $from,
        array &$recoverableChain,
    ): ?array {
        if (! $this->shouldFallbackToNextProvider($e)) {
            throw $e;
        }

        $this->logFallback($from, $next, $e);
        $recoverableChain[] = $e;

        return null;
    }

    private function shouldFallbackToNextProvider(AiTranslationUserException $e): bool
    {
        return in_array($e->translationKey, [
            'view.admin.ai.error_all_models_failed',
            'view.admin.ai.error_rate_limited',
            'view.admin.ai.error_models_unavailable',
            'view.admin.ai.error_no_models',
            'view.admin.ai.provider_error',
        ], true);
    }

    /**
     * The long HTTP 429 / quota copy (translation key view.admin.ai.error_rate_limited) is only
     * returned when every provider tried in this request failed with that key; otherwise the last
     * failure is returned so a follow-up tier is not masked by the first provider's rate-limit text.
     *
     * @param  non-empty-list<AiTranslationUserException>  $chain
     */
    private function exceptionAfterExhaustedRecoverableChain(array $chain): AiTranslationUserException
    {
        $last = $chain[array_key_last($chain)];

        foreach ($chain as $e) {
            if ($e->translationKey !== 'view.admin.ai.error_rate_limited') {
                return $last;
            }
        }

        return new AiTranslationUserException(
            'view.admin.ai.error_rate_limited',
            [],
            $last->getPrevious(),
        );
    }

    /**
     * @param  string|null  $next  null = end of chain (will rethrow last error)
     */
    private function logFallback(string $from, ?string $next, AiTranslationUserException $e): void
    {
        $nextHuman = $next !== null ? AdminAi::providerLabel($next) : null;

        $message = $nextHuman !== null
            ? sprintf(
                'Admin AI: %s did not return a usable completion (%s); trying %s next.',
                AdminAi::providerLabel($from),
                $e->translationKey,
                $nextHuman,
            )
            : sprintf(
                'Admin AI: %s did not return a usable completion (%s); '
                . 'no further provider in chain — will surface last error.',
                AdminAi::providerLabel($from),
                $e->translationKey,
            );

        $context = [
            'message' => $message,
            'from' => $from,
            'to' => $next ?? 'none',
            'reason_key' => $e->translationKey,
        ];

        if ($next === null) {
            $context['chain_eligibility'] = self::fallbackEligibilitySnapshot();
        }

        Log::info('admin.ai.chat_completion_fallback', $context);
    }

    /**
     * Non-sensitive snapshot for logs when no further provider runs (booleans only; no secrets or key hints).
     *
     * @return array<string, bool>
     */
    private static function fallbackEligibilitySnapshot(): array
    {
        $snap = [];
        foreach (AdminAi::allProviderSlugs() as $slug) {
            $snap[$slug . '_enabled'] = filter_var(config("ai.{$slug}.enabled", true), FILTER_VALIDATE_BOOL);
            $snap[$slug . '_ready'] = AdminAi::providerReady($slug);
        }

        return $snap;
    }
}
