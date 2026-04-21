<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Content;

use App\Support\Content\TranslationDisplaySql;
use InvalidArgumentException;
use Tests\TestCase;

final class TranslationDisplaySqlTest extends TestCase
{
    public function test_item_translation_subquery_rejects_invalid_items_ref(): void
    {
        $this->expectException(InvalidArgumentException::class);
        TranslationDisplaySql::itemTranslationSubquerySql('name', 'invalid_ref');
    }

    public function test_item_translation_subquery_rejects_invalid_column(): void
    {
        $this->expectException(InvalidArgumentException::class);
        // @phpstan-ignore-next-line argument.type (invalid column exercises runtime guard)
        TranslationDisplaySql::itemTranslationSubquerySql('wrong', 'items');
    }

    public function test_item_translation_subquery_contains_expected_fragments(): void
    {
        $sql = TranslationDisplaySql::itemTranslationSubquerySql('name', 'items');

        $this->assertStringContainsString('item_translations', $sql);
        $this->assertStringContainsString('items.id', $sql);
        $this->assertStringContainsString('ORDER BY FIELD', $sql);
    }

    public function test_item_category_name_subquery_rejects_wrong_table_ref(): void
    {
        $this->expectException(InvalidArgumentException::class);
        TranslationDisplaySql::itemCategoryNameSubquerySql('wrong');
    }

    public function test_tag_name_subquery_rejects_wrong_tags_ref(): void
    {
        $this->expectException(InvalidArgumentException::class);
        TranslationDisplaySql::tagNameSubquerySql('wrong_tags');
    }

    public function test_extra_info_subquery_rejects_wrong_extras_ref(): void
    {
        $this->expectException(InvalidArgumentException::class);
        TranslationDisplaySql::extraInfoSubquerySql('wrong_extras');
    }

    public function test_item_catalog_list_select_aliases_returns_two_raw_selects(): void
    {
        $aliases = TranslationDisplaySql::itemCatalogListSelectAliases();

        $this->assertCount(2, $aliases);
    }
}
