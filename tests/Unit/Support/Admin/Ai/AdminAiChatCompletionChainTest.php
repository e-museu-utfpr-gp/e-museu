<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Admin\Ai;

use App\Support\Admin\Ai\AdminAiChatCompletionChain;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

final class AdminAiChatCompletionChainTest extends TestCase
{
    public function test_ordered_slugs_deduplicates_preserving_order(): void
    {
        Config::set('ai.chat_completion.chain', ['openrouter', 'openrouter', 'groq', 'openrouter']);

        $this->assertSame(
            ['openrouter', 'groq'],
            AdminAiChatCompletionChain::orderedSlugs(),
        );
    }

    public function test_ordered_slugs_filters_unknown_then_deduplicates(): void
    {
        Config::set('ai.chat_completion.chain', ['not_a_provider', 'groq', 'groq', 'openrouter']);

        $this->assertSame(
            ['groq', 'openrouter'],
            AdminAiChatCompletionChain::orderedSlugs(),
        );
    }

    public function test_ordered_slugs_when_chain_empty_falls_back_to_catalog_order_without_duplicates(): void
    {
        Config::set('ai.chat_completion.chain', []);

        $slugs = AdminAiChatCompletionChain::orderedSlugs();
        $this->assertNotSame([], $slugs);
        $this->assertSame($slugs, array_values(array_unique($slugs, SORT_STRING)));
    }
}
