<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Content;

use App\Models\Catalog\ItemCategoryTranslation;
use App\Support\Content\ResolvedTranslation;
use Tests\TestCase;

final class ResolvedTranslationTest extends TestCase
{
    public function test_used_fallback_when_translation_exists_but_not_app_locale(): void
    {
        $row = new ItemCategoryTranslation(['name' => 'x']);
        $resolved = new ResolvedTranslation($row, 'pt_BR', false);

        $this->assertTrue($resolved->usedFallback());
    }

    public function test_used_fallback_false_when_from_app_locale(): void
    {
        $row = new ItemCategoryTranslation(['name' => 'x']);
        $resolved = new ResolvedTranslation($row, 'en', true);

        $this->assertFalse($resolved->usedFallback());
    }

    public function test_used_fallback_false_when_no_translation(): void
    {
        $resolved = new ResolvedTranslation(null, null, false);

        $this->assertFalse($resolved->usedFallback());
    }

    public function test_source_language_label_returns_null_without_code(): void
    {
        $resolved = new ResolvedTranslation(null, null, false);

        $this->assertNull($resolved->sourceLanguageLabel());
    }

    public function test_source_language_label_returns_code_when_no_translation_key(): void
    {
        $resolved = new ResolvedTranslation(new ItemCategoryTranslation(), 'zz_unknown_locale', false);

        $this->assertSame('zz_unknown_locale', $resolved->sourceLanguageLabel());
    }
}
