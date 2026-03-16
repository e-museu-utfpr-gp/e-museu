<?php

namespace App\Support;

class AdminIndexConfig
{
    /**
     * @return array{
     *     baseTable: string,
     *     searchBaseTable: string,
     *     searchSpecial: array<string, array{table: string, column: string}>,
     *     sortSpecial: array<string, string>,
     *     booleanColumns: array<int, string>
     * }
     */
    public static function extras(): array
    {
        return [
            'baseTable' => 'extras',
            'searchBaseTable' => 'items',
            'searchSpecial' => [
                'collaborator_id' => ['table' => 'collaborators', 'column' => 'contact'],
                'item_id' => ['table' => 'items', 'column' => 'name'],
            ],
            'sortSpecial' => [
                'collaborator_id' => 'collaborators.contact',
                'item_id' => 'items.name',
            ],
            'booleanColumns' => ['validation'],
        ];
    }

    /**
     * @return array{
     *     baseTable: string,
     *     searchSpecial: array<string, array{table: string, column: string}>,
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
                'category_id' => ['table' => 'item_categories', 'column' => 'name'],
            ],
            'sortSpecial' => [
                'collaborator_id' => 'collaborators.contact',
                'category_id' => 'item_categories.name',
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
     * @return array{baseTable: string}
     */
    public static function itemCategories(): array
    {
        return [
            'baseTable' => 'item_categories',
        ];
    }

    /**
     * @return array{baseTable: string}
     */
    public static function tagCategories(): array
    {
        return [
            'baseTable' => 'tag_categories',
        ];
    }

    /**
     * @return array{
     *     baseTable: string,
     *     searchSpecial: array<string, array{table: string, column: string}>,
     *     sortSpecial: array<string, string>,
     *     booleanColumns: array<int, string>
     * }
     */
    public static function itemComponents(): array
    {
        return [
            'baseTable' => 'item_component',
            'searchSpecial' => [
                'item_id' => ['table' => 'item', 'column' => 'name'],
                'component_id' => ['table' => 'component', 'column' => 'name'],
            ],
            'sortSpecial' => [
                'item_id' => 'item.name',
                'component_id' => 'component.name',
            ],
            'booleanColumns' => ['validation'],
        ];
    }

    /**
     * @return array{
     *     baseTable: string,
     *     searchSpecial: array<string, array{table: string, column: string}>,
     *     sortSpecial: array<string, string>,
     *     booleanColumns: array<int, string>
     * }
     */
    public static function itemTags(): array
    {
        return [
            'baseTable' => 'item_tag',
            'searchSpecial' => [
                'item_id' => ['table' => 'items', 'column' => 'name'],
                'tag_id' => ['table' => 'tags', 'column' => 'name'],
            ],
            'sortSpecial' => [
                'item_id' => 'items.name',
                'tag_id' => 'tags.name',
            ],
            'booleanColumns' => ['validation'],
        ];
    }

    /**
     * @return array{
     *     baseTable: string,
     *     searchSpecial: array<string, array{table: string, column: string}>,
     *     sortSpecial: array<string, string>,
     *     booleanColumns: array<int, string>
     * }
     */
    public static function tags(): array
    {
        return [
            'baseTable' => 'tags',
            'searchSpecial' => [
                'tag_category_id' => ['table' => 'tag_categories', 'column' => 'name'],
            ],
            'sortSpecial' => [
                'tag_category_id' => 'tag_categories.name',
            ],
            'booleanColumns' => ['validation'],
        ];
    }
}
