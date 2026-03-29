<?php

namespace App\Support\Content;

use InvalidArgumentException;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

/**
 * Correlated subqueries for ordering/filtering/display by translated strings (MySQL FIELD fallback order).
 */
final class TranslationDisplaySql
{
    /** @var list<string> */
    private const ITEM_PARENT_TABLE_REFS = ['items', 'item', 'component'];

    private const ITEM_CATEGORY_TABLE_REF = 'item_categories';

    private const TAG_TABLE_REF = 'tags';

    private const TAG_CATEGORY_TABLE_REF = 'tag_categories';

    private const EXTRA_TABLE_REF = 'extras';

    public static function itemNameExpression(): Expression
    {
        return DB::raw(self::itemNameSubquerySql('items'));
    }

    public static function itemDescriptionExpression(): Expression
    {
        return DB::raw(self::itemTranslationSubquerySql('description', 'items'));
    }

    /**
     * Subquery: resolved `name` for an item row (`{itemsRef}.id` = item_translations.item_id).
     */
    public static function itemNameSubquerySql(string $itemsTableRef): string
    {
        return self::itemTranslationSubquerySql('name', $itemsTableRef);
    }

    /**
     * @param  'name'|'description'|'history'|'detail'  $column
     */
    public static function itemTranslationSubquerySql(string $column, string $itemsTableRef): string
    {
        if (! in_array($itemsTableRef, self::ITEM_PARENT_TABLE_REFS, true)) {
            throw new InvalidArgumentException('Invalid items table reference for item translation subquery.');
        }
        $allowed = ['name', 'description', 'history', 'detail'];
        if (! in_array($column, $allowed, true)) {
            throw new InvalidArgumentException("Invalid item translation column: {$column}");
        }
        $list = ContentLocaleFallback::fieldListSql();

        return '('
            . "SELECT it.{$column} FROM item_translations it "
            . 'INNER JOIN languages l ON l.id = it.language_id '
            . "WHERE it.item_id = {$itemsTableRef}.id "
            . "ORDER BY FIELD(l.code, {$list}) LIMIT 1)";
    }

    public static function itemCategoryNameSubquerySql(string $categoryTableRef = self::ITEM_CATEGORY_TABLE_REF): string
    {
        if ($categoryTableRef !== self::ITEM_CATEGORY_TABLE_REF) {
            throw new InvalidArgumentException('Invalid item category table reference for translation subquery.');
        }
        $list = ContentLocaleFallback::fieldListSql();

        return '('
            . 'SELECT ict.name FROM item_category_translations ict '
            . 'INNER JOIN languages l ON l.id = ict.language_id '
            . "WHERE ict.item_category_id = {$categoryTableRef}.id "
            . "ORDER BY FIELD(l.code, {$list}) LIMIT 1)";
    }

    public static function tagNameSubquerySql(string $tagsTableRef = self::TAG_TABLE_REF): string
    {
        if ($tagsTableRef !== self::TAG_TABLE_REF) {
            throw new InvalidArgumentException('Invalid tags table reference for translation subquery.');
        }
        $list = ContentLocaleFallback::fieldListSql();

        return '('
            . 'SELECT tt.name FROM tag_translations tt '
            . 'INNER JOIN languages l ON l.id = tt.language_id '
            . "WHERE tt.tag_id = {$tagsTableRef}.id "
            . "ORDER BY FIELD(l.code, {$list}) LIMIT 1)";
    }

    public static function tagCategoryNameSubquerySql(
        string $tagCategoriesTableRef = self::TAG_CATEGORY_TABLE_REF
    ): string {
        if ($tagCategoriesTableRef !== self::TAG_CATEGORY_TABLE_REF) {
            throw new InvalidArgumentException('Invalid tag category table reference for translation subquery.');
        }
        $list = ContentLocaleFallback::fieldListSql();

        return '('
            . 'SELECT tct.name FROM tag_category_translations tct '
            . 'INNER JOIN languages l ON l.id = tct.language_id '
            . "WHERE tct.tag_category_id = {$tagCategoriesTableRef}.id "
            . "ORDER BY FIELD(l.code, {$list}) LIMIT 1)";
    }

    public static function extraInfoSubquerySql(string $extrasTableRef = self::EXTRA_TABLE_REF): string
    {
        if ($extrasTableRef !== self::EXTRA_TABLE_REF) {
            throw new InvalidArgumentException('Invalid extras table reference for translation subquery.');
        }
        $list = ContentLocaleFallback::fieldListSql();

        return '('
            . 'SELECT et.info FROM extra_translations et '
            . 'INNER JOIN languages l ON l.id = et.language_id '
            . "WHERE et.extra_id = {$extrasTableRef}.id "
            . "ORDER BY FIELD(l.code, {$list}) LIMIT 1)";
    }

    /**
     * Virtual columns for public catalog list cards (name + description by locale fallback).
     *
     * @return list<Expression>
     */
    public static function itemCatalogListSelectAliases(): array
    {
        return [
            DB::raw(self::itemTranslationSubquerySql('name', 'items') . ' AS name'),
            DB::raw(self::itemTranslationSubquerySql('description', 'items') . ' AS description'),
        ];
    }
}
