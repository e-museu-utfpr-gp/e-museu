<?php

namespace App\Support\Admin;

use App\Support\Content\TranslationDisplaySql;

class AdminIndexConfig
{
    /**
     * @return array{
     *     baseTable: string,
     *     searchSpecial?: array<string, array{table: string, column: string}>,
     *     searchLikeSubquery?: array<string, string>,
     *     sortSpecial?: array<string, string>,
     *     booleanColumns: array<int, string>
     * }
     */
    public static function extras(): array
    {
        return [
            'baseTable' => 'extras',
            'searchSpecial' => [
                'collaborator_id' => ['table' => 'collaborators', 'column' => 'contact'],
            ],
            'searchLikeSubquery' => [
                'info' => TranslationDisplaySql::extraInfoSubquerySql('extras'),
                'item_id' => TranslationDisplaySql::itemNameSubquerySql('items'),
            ],
            'sortSpecial' => [
                'collaborator_id' => 'collaborators.contact',
                'item_id' => 'item_name',
                'info' => 'info',
            ],
            'booleanColumns' => ['validation'],
        ];
    }

    /**
     * @return array{
     *     baseTable: string,
     *     searchSpecial: array<string, array{table: string, column: string}>,
     *     searchLikeSubquery: array<string, string>,
     *     sortSubquery: array<string, string>,
     *     sortSpecial: array<string, string>,
     *     booleanColumns: array<int, string>
     * }
     */
    public static function items(): array
    {
        return [
            'baseTable' => 'items',
            'searchSpecial' => [
                'collaborator_id' => ['table' => 'collaborators', 'column' => 'contact'],
            ],
            'searchLikeSubquery' => [
                'name' => TranslationDisplaySql::itemTranslationSubquerySql('name', 'items'),
                'description' => TranslationDisplaySql::itemTranslationSubquerySql('description', 'items'),
                'history' => TranslationDisplaySql::itemTranslationSubquerySql('history', 'items'),
                'detail' => TranslationDisplaySql::itemTranslationSubquerySql('detail', 'items'),
                'detalhes' => TranslationDisplaySql::itemTranslationSubquerySql('detail', 'items'),
                'category_id' => TranslationDisplaySql::itemCategoryNameSubquerySql('item_categories'),
            ],
            'sortSubquery' => [
                'name' => TranslationDisplaySql::itemTranslationSubquerySql('name', 'items'),
                'description' => TranslationDisplaySql::itemTranslationSubquerySql('description', 'items'),
                'history' => TranslationDisplaySql::itemTranslationSubquerySql('history', 'items'),
                'detail' => TranslationDisplaySql::itemTranslationSubquerySql('detail', 'items'),
            ],
            'sortSpecial' => [
                'collaborator_id' => 'collaborators.contact',
                'category_id' => 'item_category_name',
            ],
            'booleanColumns' => ['validation'],
        ];
    }

    /**
     * @return array{
     *     baseTable: string,
     *     booleanColumns: array<int, string>,
     *     exactColumns: array<int, string>
     * }
     */
    public static function collaborators(): array
    {
        return [
            'baseTable' => 'collaborators',
            'booleanColumns' => ['blocked'],
            'exactColumns' => ['role'],
        ];
    }

    /**
     * @return array{baseTable: string}
     */
    public static function admins(): array
    {
        return [
            'baseTable' => 'admins',
        ];
    }

    /**
     * @return array{
     *     baseTable: string,
     *     searchLikeSubquery?: array<string, string>,
     *     sortSpecial?: array<string, string>
     * }
     */
    public static function itemCategories(): array
    {
        return [
            'baseTable' => 'item_categories',
            'searchLikeSubquery' => [
                'name' => TranslationDisplaySql::itemCategoryNameSubquerySql('item_categories'),
            ],
            'sortSpecial' => [
                'name' => 'name',
            ],
        ];
    }

    /**
     * @return array{
     *     baseTable: string,
     *     searchLikeSubquery?: array<string, string>,
     *     sortSpecial?: array<string, string>
     * }
     */
    public static function tagCategories(): array
    {
        return [
            'baseTable' => 'tag_categories',
            'searchLikeSubquery' => [
                'name' => TranslationDisplaySql::tagCategoryNameSubquerySql('tag_categories'),
            ],
            'sortSpecial' => [
                'name' => 'name',
            ],
        ];
    }

    /**
     * @return array{
     *     baseTable: string,
     *     searchLikeSubquery?: array<string, string>,
     *     sortSpecial?: array<string, string>,
     *     booleanColumns: array<int, string>
     * }
     */
    public static function itemComponents(): array
    {
        return [
            'baseTable' => 'item_component',
            'searchLikeSubquery' => [
                'item_id' => TranslationDisplaySql::itemNameSubquerySql('item'),
                'component_id' => TranslationDisplaySql::itemNameSubquerySql('component'),
            ],
            'sortSpecial' => [
                'item_id' => 'item_name',
                'component_id' => 'component_name',
            ],
            'booleanColumns' => ['validation'],
        ];
    }

    /**
     * @return array{
     *     baseTable: string,
     *     searchLikeSubquery?: array<string, string>,
     *     sortSpecial?: array<string, string>,
     *     booleanColumns: array<int, string>
     * }
     */
    public static function itemTags(): array
    {
        return [
            'baseTable' => 'item_tag',
            'searchLikeSubquery' => [
                'item_id' => TranslationDisplaySql::itemNameSubquerySql('items'),
                'tag_id' => TranslationDisplaySql::tagNameSubquerySql('tags'),
            ],
            'sortSpecial' => [
                'item_id' => 'item_name',
                'tag_id' => 'tag_name',
            ],
            'booleanColumns' => ['validation'],
        ];
    }

    /**
     * @return array{
     *     baseTable: string,
     *     searchLikeSubquery?: array<string, string>,
     *     sortSpecial?: array<string, string>,
     *     booleanColumns: array<int, string>
     * }
     */
    public static function tags(): array
    {
        return [
            'baseTable' => 'tags',
            'searchLikeSubquery' => [
                'name' => TranslationDisplaySql::tagNameSubquerySql('tags'),
                'tag_category_id' => TranslationDisplaySql::tagCategoryNameSubquerySql('tag_categories'),
            ],
            'sortSpecial' => [
                'name' => 'tag_name',
                'tag_category_id' => 'category_name',
            ],
            'booleanColumns' => ['validation'],
        ];
    }
}
