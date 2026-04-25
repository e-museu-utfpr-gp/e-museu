<?php

declare(strict_types=1);

namespace App\Support\Admin\Ai;

/**
 * Discovers provider section keys under {@see config('ai')}.
 */
final class AdminAiProviderCatalog
{
    /** @var list<string> */
    private const NON_PROVIDER_CONFIG_KEYS = ['rate_limit', 'translation', 'chat_completion'];

    /**
     * @return list<string>
     */
    public static function allProviderSlugs(): array
    {
        $providers = [];
        $allConfig = config('ai', []);
        if (! is_array($allConfig)) {
            return [];
        }

        foreach ($allConfig as $key => $section) {
            if (! is_string($key) || in_array($key, self::NON_PROVIDER_CONFIG_KEYS, true) || ! is_array($section)) {
                continue;
            }

            if (
                array_key_exists('enabled', $section)
                && array_key_exists('api_key', $section)
                && array_key_exists('models', $section)
            ) {
                $providers[] = $key;
            }
        }

        return $providers;
    }
}
