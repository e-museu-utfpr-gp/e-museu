<?php

namespace Tests\Unit\Support\Admin;

use App\Models\Catalog\ItemCategory;
use App\Models\Language;
use App\Support\Admin\AdminEditHeadingLocale;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('mysql')]
final class AdminEditHeadingLocaleTest extends ServiceMysqlTestCase
{
    public function test_preferred_content_tab_language_id_matches_language_helper(): void
    {
        config(['app.locale' => 'pt_BR', 'app.fallback_locale' => 'en']);
        app()->setLocale('pt_BR');

        $this->assertSame(
            Language::idForPreferredFormLocale(),
            AdminEditHeadingLocale::preferredContentTabLanguageId()
        );
    }

    public function test_resolve_for_prefers_heading_in_preferred_form_locale(): void
    {
        config(['app.locale' => 'en', 'app.fallback_locale' => 'pt_BR']);
        app()->setLocale('en');

        $preferredId = Language::idForPreferredFormLocale();
        $category = ItemCategory::factory()->create();
        $category->load('translations.language');

        $resolver = new AdminEditHeadingLocale();
        $resolved = $resolver->resolveFor($category);

        $this->assertSame($preferredId, $resolved['preferredContentTabLanguageId']);
        $this->assertNotNull($resolved['headingTranslation']);
        $heading = $resolved['headingTranslation'];
        $this->assertSame(
            $heading->language?->code,
            $resolved['preferredContentTabLanguageCode']
        );
    }
}
