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

    /** @return list<string> */
    public static function chatCompletionChainSlugs(): array
    {
        return AdminAiChatCompletionChain::orderedSlugs();
    }

    public static function firstChatCompletionChainSlug(): ?string
    {
        $chain = self::chatCompletionChainSlugs();

        return $chain[0] ?? null;
    }

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
        foreach (AdminAiProviderCatalog::allProviderSlugs() as $slug) {
            if (filter_var(config("ai.{$slug}.enabled", true), FILTER_VALIDATE_BOOL)) {
                return true;
            }
        }

        return false;
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

    /** @return list<string> */
    public static function allProviderSlugs(): array
    {
        return AdminAiProviderCatalog::allProviderSlugs();
    }

    private static function hasAnyChatProviderConfigured(): bool
    {
        foreach (AdminAiProviderCatalog::allProviderSlugs() as $slug) {
            if (self::providerReady($slug)) {
                return true;
            }
        }

        return false;
    }

    /** @return list<string> */
    public static function configuredProviderSlugs(): array
    {
        $out = [];
        foreach (AdminAiProviderCatalog::allProviderSlugs() as $slug) {
            if (self::providerReady($slug)) {
                $out[] = $slug;
            }
        }

        return $out;
    }

    /**
     * True when this slug is a configured AI provider block and enabled with key + models.
     */
    public static function providerReady(string $slug): bool
    {
        if (! in_array($slug, AdminAiProviderCatalog::allProviderSlugs(), true)) {
            return false;
        }

        return self::providerSectionReady($slug);
    }

    /**
     * Human-facing provider name from {@see config("ai.{slug}.human_label")} (set via .env per provider block).
     * Falls back to a short formatted slug when the block has no label.
     */
    public static function providerLabel(string $slug): string
    {
        $fromConfig = trim((string) config('ai.' . $slug . '.human_label', ''));
        if ($fromConfig !== '') {
            return $fromConfig;
        }

        return (string) __('view.admin.ai.provider_default', [
            'name' => self::formatProviderSlugForDisplay($slug),
        ]);
    }

    private static function formatProviderSlugForDisplay(string $slug): string
    {
        $slug = trim(str_replace(['-', '_'], ' ', $slug));
        if ($slug === '') {
            return '?';
        }
        if (preg_match('/^[\p{P}\p{S}\s]+$/u', $slug)) {
            return '?';
        }

        return mb_convert_case($slug, MB_CASE_TITLE, 'UTF-8');
    }

    /** @return list<string> */
    public static function configuredModelsFor(string $providerSlug): array
    {
        if (! in_array($providerSlug, AdminAiProviderCatalog::allProviderSlugs(), true)) {
            return [];
        }

        $raw = config("ai.{$providerSlug}.models", []);
        if (! is_array($raw)) {
            return [];
        }

        $models = [];
        foreach ($raw as $model) {
            if (! is_string($model)) {
                continue;
            }
            $model = trim($model);
            if ($model !== '') {
                $models[] = $model;
            }
        }

        return $models;
    }

    private static function providerSectionReady(string $slug): bool
    {
        if (! filter_var(config("ai.{$slug}.enabled", true), FILTER_VALIDATE_BOOL)) {
            return false;
        }

        if (trim((string) config("ai.{$slug}.api_key")) === '') {
            return false;
        }

        if (trim((string) config("ai.{$slug}.provider_url")) === '') {
            return false;
        }

        $models = config("ai.{$slug}.models", []);

        if (! is_array($models) || $models === []) {
            return false;
        }

        return self::configuredModelsFor($slug) !== [];
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
