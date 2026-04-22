<?php

declare(strict_types=1);

namespace App\Support\Admin\Ai;

use Illuminate\Support\Facades\Log;

/**
 * Admin AI helpers derived from application configuration.
 */
final class AdminAi
{
    private static bool $warnedMissingChatProviders = false;

    /**
     * Whether admin AI translation UI should show (at least one provider enabled, with key and models).
     */
    public static function translationUiEnabled(): bool
    {
        return self::hasAnyChatProviderConfigured();
    }

    /**
     * True when any provider's *_ENABLED env flag is on (independent of API keys).
     */
    public static function anyAdminAiProviderSwitchEnabled(): bool
    {
        return filter_var(config('ai.github_models.enabled', true), FILTER_VALIDATE_BOOL)
            || filter_var(config('ai.openrouter.enabled', true), FILTER_VALIDATE_BOOL)
            || filter_var(config('ai.groq.enabled', true), FILTER_VALIDATE_BOOL);
    }

    /**
     * @return 'disabled'|'not_configured'|null  null when the translation POST may proceed.
     */
    public static function translationEndpointBlockReason(): ?string
    {
        if (self::hasAnyChatProviderConfigured()) {
            return null;
        }

        if (! self::anyAdminAiProviderSwitchEnabled()) {
            return 'disabled';
        }

        self::warnOnceIfProvidersRequestedButNoneReady();

        return 'not_configured';
    }

    public static function hasAnyChatProviderConfigured(): bool
    {
        return self::githubModelsReady()
            || self::openRouterReady()
            || self::groqReady();
    }

    public static function githubModelsReady(): bool
    {
        if (! filter_var(config('ai.github_models.enabled', true), FILTER_VALIDATE_BOOL)) {
            return false;
        }

        if (trim((string) config('ai.github_models.api_key')) === '') {
            return false;
        }

        $models = config('ai.github_models.models', []);

        return is_array($models) && $models !== [];
    }

    public static function openRouterReady(): bool
    {
        if (! filter_var(config('ai.openrouter.enabled', true), FILTER_VALIDATE_BOOL)) {
            return false;
        }

        if (trim((string) config('ai.openrouter.api_key')) === '') {
            return false;
        }

        $models = config('ai.openrouter.models', []);

        return is_array($models) && $models !== [];
    }

    public static function groqReady(): bool
    {
        if (! filter_var(config('ai.groq.enabled', true), FILTER_VALIDATE_BOOL)) {
            return false;
        }

        if (trim((string) config('ai.groq.api_key')) === '') {
            return false;
        }

        $models = config('ai.groq.models', []);

        return is_array($models) && $models !== [];
    }

    public static function warnOnceIfProvidersRequestedButNoneReady(): void
    {
        if (self::$warnedMissingChatProviders || ! self::anyAdminAiProviderSwitchEnabled()) {
            return;
        }

        self::$warnedMissingChatProviders = true;

        Log::warning('admin.ai.no_chat_providers_configured', [
            'message' => 'At least one admin AI provider is enabled but none have a valid API key and model list; '
                . 'translation assist is unavailable.',
        ]);
    }

    /**
     * @internal
     */
    public static function resetMissingProvidersWarningForTesting(): void
    {
        self::$warnedMissingChatProviders = false;
    }
}
