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
}
