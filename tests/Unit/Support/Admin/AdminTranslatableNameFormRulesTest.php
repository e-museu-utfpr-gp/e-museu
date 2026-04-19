<?php

namespace Tests\Unit\Support\Admin;

use App\Support\Admin\AdminTranslatableNameFormRules;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('mysql')]
final class AdminTranslatableNameFormRulesTest extends ServiceMysqlTestCase
{
    public function test_base_translation_rules_structure(): void
    {
        $rules = AdminTranslatableNameFormRules::baseTranslationRules();

        $this->assertArrayHasKey('translations', $rules);
        $this->assertSame(['required', 'array'], $rules['translations']);
    }

    public function test_with_scoped_unique_name_columns_adds_locale_name_keys_without_parent(): void
    {
        $base = AdminTranslatableNameFormRules::baseTranslationRules();
        $merged = AdminTranslatableNameFormRules::withScopedUniqueNameColumns(
            $base,
            null,
            'tag_translations',
            'tag_id'
        );

        $this->assertArrayHasKey('translations.pt_BR.name', $merged);
        $this->assertArrayHasKey('translations.en.name', $merged);
    }

    public function test_normalize_empty_name_strings_to_null(): void
    {
        $raw = [
            'pt_BR' => ['name' => ''],
            'en' => ['name' => ' kept '],
            'universal' => ['other' => 1],
        ];

        $out = AdminTranslatableNameFormRules::normalizeEmptyNameStringsToNull($raw);

        $this->assertNull($out['pt_BR']['name']);
        $this->assertSame(' kept ', $out['en']['name']);
    }

    public function test_validate_at_least_one_non_empty_name_adds_error_when_all_blank(): void
    {
        $validator = Validator::make([], []);
        $translations = [
            'pt_BR' => ['name' => '  '],
            'en' => ['name' => ''],
        ];

        AdminTranslatableNameFormRules::validateAtLeastOneNonEmptyName(
            $validator,
            $translations,
            'validation.item_categories.translations.at_least_one_locale'
        );

        $this->assertTrue($validator->errors()->has('translations'));
    }

    public function test_validate_at_least_one_non_empty_name_passes_when_any_present(): void
    {
        $validator = Validator::make([], []);
        $translations = [
            'pt_BR' => ['name' => 'ok'],
        ];

        AdminTranslatableNameFormRules::validateAtLeastOneNonEmptyName(
            $validator,
            $translations,
            'validation.item_categories.translations.at_least_one_locale'
        );

        $this->assertFalse($validator->errors()->has('translations'));
    }
}
