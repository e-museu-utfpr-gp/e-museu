<?php

declare(strict_types=1);

namespace App\Support\Admin\Ai;

use Illuminate\Support\Facades\Log;

/**
 * Resolves the ordered provider slug list for chat completion (env chain + fallback).
 */
final class AdminAiChatCompletionChain
{
    /**
     * @return list<string>
     */
    public static function orderedSlugs(): array
    {
        $rawChain = config('ai.chat_completion.chain');
        if (! is_array($rawChain)) {
            $rawChain = [];
        }

        $allowed = array_flip(AdminAiProviderCatalog::allProviderSlugs());
        $out = [];
        foreach ($rawChain as $slug) {
            if (! is_string($slug)) {
                continue;
            }
            $slug = trim($slug);
            if ($slug === '' || ! isset($allowed[$slug])) {
                continue;
            }
            $out[] = $slug;
        }

        if ($out === []) {
            if ($rawChain !== []) {
                Log::warning('admin.ai.chat_completion_chain_fallback', [
                    'message' => 'AI_CHAT_COMPLETION_CHAIN produced no valid provider slugs; '
                        . 'falling back to iteration order of all provider blocks in config.',
                    'raw_chain' => $rawChain,
                ]);
            }
            foreach (AdminAiProviderCatalog::allProviderSlugs() as $slug) {
                $out[] = $slug;
            }
        }

        return self::uniqueSlugsPreservingOrder($out);
    }

    /**
     * @param  list<string>  $slugs
     * @return list<string>
     */
    private static function uniqueSlugsPreservingOrder(array $slugs): array
    {
        $seen = [];
        $out = [];
        foreach ($slugs as $slug) {
            if (isset($seen[$slug])) {
                continue;
            }
            $seen[$slug] = true;
            $out[] = $slug;
        }

        return $out;
    }
}
