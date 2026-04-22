<?php

declare(strict_types=1);

namespace App\Actions\Admin\Ai\AdminChatCompletionAction\Concerns\Fallback;

use App\Exceptions\AiTranslationUserException;
use App\Support\Admin\Ai\AdminAi;
use Illuminate\Support\Facades\Log;

/**
 * Recoverable-error chain, structured logging between providers, and final exception shaping.
 * Used only by {@see \App\Actions\Admin\Ai\AdminChatCompletionAction\AdminChatCompletionAction}.
 */
trait HandlesRecoverableChatCompletionFallback
{
    /**
     * @param  'groq'|'github_models'|null  $next
     * @param  'openrouter'|'groq'|'github_models'  $from
     * @param  list<AiTranslationUserException>  $recoverableChain
     * @return array{content: string, model: string}|null  null when caller should try the next tier
     *
     * @throws AiTranslationUserException
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod) Called from traits in Concerns/Providers.
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
     * @param  'openrouter'|'groq'|'github_models'  $from
     * @param  'groq'|'github_models'|null  $next  null = end of chain (will rethrow last error)
     */
    private function logFallback(string $from, ?string $next, AiTranslationUserException $e): void
    {
        $nextHuman = match ($next) {
            'groq' => 'Groq',
            'github_models' => 'GitHub Models',
            default => null,
        };

        $message = $nextHuman !== null
            ? sprintf(
                'Admin AI: %s did not return a usable completion (%s); trying %s next.',
                self::providerLabel($from),
                $e->translationKey,
                $nextHuman,
            )
            : sprintf(
                'Admin AI: %s did not return a usable completion (%s); '
                . 'no further provider in chain — will surface last error.',
                self::providerLabel($from),
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
        return [
            'openrouter_enabled' => filter_var(config('ai.openrouter.enabled', true), FILTER_VALIDATE_BOOL),
            'openrouter_ready' => AdminAi::openRouterReady(),
            'groq_enabled' => filter_var(config('ai.groq.enabled', true), FILTER_VALIDATE_BOOL),
            'groq_ready' => AdminAi::groqReady(),
            'github_models_enabled' => filter_var(config('ai.github_models.enabled', true), FILTER_VALIDATE_BOOL),
            'github_models_ready' => AdminAi::githubModelsReady(),
        ];
    }

    private static function providerLabel(string $slug): string
    {
        /** @var array<string, string> $labels */
        $labels = [
            'openrouter' => 'OpenRouter',
            'groq' => 'Groq',
            'github_models' => 'GitHub Models',
        ];

        return $labels[$slug] ?? $slug;
    }
}
