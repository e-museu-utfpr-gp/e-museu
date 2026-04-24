<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Admin\Ai;

use App\Support\Admin\Ai\AdminAiProviderCatalog;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

final class AdminAiProviderCatalogTest extends TestCase
{
    public function test_all_provider_slugs_excludes_non_provider_top_level_keys(): void
    {
        Config::set('ai.chat_completion', ['chain' => ['openrouter']]);
        Config::set('ai.translation', ['max_source_chars' => 1000]);
        Config::set('ai.rate_limit', ['per_minute' => 1]);
        Config::set('ai.openrouter', [
            'enabled' => true,
            'api_key' => 'k',
            'models' => ['m'],
        ]);

        $slugs = AdminAiProviderCatalog::allProviderSlugs();

        $this->assertContains('openrouter', $slugs);
        $this->assertNotContains('rate_limit', $slugs);
        $this->assertNotContains('translation', $slugs);
        $this->assertNotContains('chat_completion', $slugs);
    }

    public function test_section_missing_models_is_not_listed_as_provider(): void
    {
        Config::set('ai.partial_block', [
            'enabled' => true,
            'api_key' => 'k',
        ]);

        $slugs = AdminAiProviderCatalog::allProviderSlugs();

        $this->assertNotContains('partial_block', $slugs);
    }

    public function test_section_that_is_not_array_is_skipped(): void
    {
        Config::set('ai.not_an_array', 'scalar');

        $slugs = AdminAiProviderCatalog::allProviderSlugs();

        $this->assertNotContains('not_an_array', $slugs);
    }
}
