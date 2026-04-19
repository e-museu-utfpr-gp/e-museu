<?php

namespace Tests\Unit\Services\Catalog;

use App\Services\Catalog\ContributionContentLocaleService;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('services')]
class ContributionContentLocaleServiceTest extends ServiceMysqlTestCase
{
    public function test_language_id_for_validated_code_returns_language_primary_key(): void
    {
        $svc = app(ContributionContentLocaleService::class);
        $id = $svc->languageIdForValidatedCode('pt_BR');

        $this->assertGreaterThan(0, $id);
    }

    public function test_form_options_returns_contribution_languages_and_default_locale_string(): void
    {
        $svc = app(ContributionContentLocaleService::class);
        $options = $svc->formOptions();

        $this->assertTrue($options['contributionLanguages']->isNotEmpty());
        $this->assertNotSame('', $options['defaultContentLocale']);
    }
}
