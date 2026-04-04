<?php

namespace Tests\Unit\Content;

use App\Enums\Content\ContentLanguage;
use App\Support\Content\ContentLocaleFallback;
use Tests\TestCase;

class ContentLocaleFallbackTest extends TestCase
{
    public function test_normalizes_plain_pt_to_pt_br(): void
    {
        config(['app.locale' => 'pt', 'app.fallback_locale' => 'en']);
        app()->setLocale('pt');

        $this->assertSame(ContentLanguage::PT_BR->value, ContentLocaleFallback::normalizedAppLocaleCode());
    }

    public function test_ordered_codes_starts_with_normalized_locale(): void
    {
        config(['app.locale' => 'pt_BR', 'app.fallback_locale' => 'en']);
        app()->setLocale('pt_BR');

        $codes = ContentLocaleFallback::orderedCodes();
        $this->assertSame(ContentLanguage::PT_BR->value, $codes[0]);
    }

    public function test_normalizes_english_locale_prefix_to_en(): void
    {
        config(['app.locale' => 'en_GB', 'app.fallback_locale' => 'pt_BR']);
        app()->setLocale('en_GB');

        $this->assertSame(ContentLanguage::EN->value, ContentLocaleFallback::normalizedAppLocaleCode());
    }

    public function test_ordered_codes_includes_neutral_after_active_locale(): void
    {
        config(['app.locale' => 'en', 'app.fallback_locale' => 'pt_BR']);
        app()->setLocale('en');

        $codes = ContentLocaleFallback::orderedCodes();
        $this->assertSame(ContentLanguage::EN->value, $codes[0]);
        $this->assertContains(ContentLanguage::NEUTRAL->value, $codes);
        $neutralIndex = array_search(ContentLanguage::NEUTRAL->value, $codes, true);
        $this->assertNotFalse($neutralIndex);
        $this->assertGreaterThan(0, $neutralIndex);
    }

    public function test_ordered_codes_deduplicates_when_fallback_matches_normalized(): void
    {
        config(['app.locale' => 'pt_BR', 'app.fallback_locale' => 'pt_BR']);
        app()->setLocale('pt_BR');

        $codes = ContentLocaleFallback::orderedCodes();
        $ptCount = count(array_keys($codes, ContentLanguage::PT_BR->value, true));
        $this->assertSame(1, $ptCount);
    }
}
