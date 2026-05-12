<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\Content\ContentLanguage;
use App\Models\Language;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\Concerns\RequiresMysqlDriverConnection;
use Tests\Unit\Services\ServiceMysqlTestCase;

/**
 * Ensures {@see ContentLanguage} cases stay aligned with seeded `languages.code` rows
 * (required for QR poster name resolution and catalog locale logic).
 */
#[Group('mysql')]
final class ContentLanguageMatchesLanguagesTableTest extends ServiceMysqlTestCase
{
    use RequiresMysqlDriverConnection;

    public function test_each_content_language_enum_case_has_a_languages_table_row(): void
    {
        foreach (ContentLanguage::cases() as $case) {
            $this->assertNotNull(
                Language::tryIdForCode($case->value),
                sprintf('Missing `languages` row for ContentLanguage::%s (%s).', $case->name, $case->value),
            );
        }
    }
}
