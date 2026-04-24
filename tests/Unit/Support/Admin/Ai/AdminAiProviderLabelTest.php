<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Admin\Ai;

use App\Support\Admin\Ai\AdminAi;
use Illuminate\Support\Facades\Config;
use ReflectionClass;
use Tests\TestCase;

final class AdminAiProviderLabelTest extends TestCase
{
    public function test_provider_label_uses_human_label_from_config(): void
    {
        Config::set('ai.openrouter.human_label', 'Custom label');

        $this->assertSame('Custom label', AdminAi::providerLabel('openrouter'));
    }

    public function test_provider_label_falls_back_when_human_label_empty(): void
    {
        Config::set('ai.openrouter.human_label', '');

        $this->assertSame('Openrouter', AdminAi::providerLabel('openrouter'));
    }

    public function test_format_provider_slug_for_display_returns_placeholder_for_punctuation_only(): void
    {
        $m = (new ReflectionClass(AdminAi::class))->getMethod('formatProviderSlugForDisplay');
        $m->setAccessible(true);

        $this->assertSame('?', $m->invoke(null, '###'));
        $this->assertSame('?', $m->invoke(null, '   '));
    }
}
