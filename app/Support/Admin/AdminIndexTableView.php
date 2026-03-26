<?php

namespace App\Support\Admin;

class AdminIndexTableView
{
    /**
     * @param  array<string, mixed>  $config
     * @return array<int, string>
     */
    private static function booleanColumnsFromConfig(array $config): array
    {
        return $config['booleanColumns'] ?? [];
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array{
     *     searchOptions: list<array{value: string, label: string}>,
     *     sortColumns: list<array{sort: string|null, label: string}>,
     *     searchBooleanColumns: array<int, string>
     * }
     */
    private static function basicNameIndex(string $prefix, array $config): array
    {
        return [
            'searchOptions' => [
                ['value' => 'id', 'label' => __("$prefix.search_option_id")],
                ['value' => 'name', 'label' => __("$prefix.search_option_name")],
                ['value' => 'created_at', 'label' => __("$prefix.search_option_created_at")],
                ['value' => 'updated_at', 'label' => __("$prefix.search_option_updated_at")],
            ],
            'sortColumns' => [
                ['sort' => 'id', 'label' => __("$prefix.sort_id")],
                ['sort' => 'name', 'label' => __("$prefix.sort_name")],
                ['sort' => 'created_at', 'label' => __("$prefix.sort_created_at")],
                ['sort' => 'updated_at', 'label' => __("$prefix.sort_updated_at")],
                ['sort' => null, 'label' => ''],
            ],
            'searchBooleanColumns' => self::booleanColumnsFromConfig($config),
        ];
    }

    /**
     * @return array{
     *     searchOptions: list<array{value: string, label: string}>,
     *     sortColumns: list<array{sort: string|null, label: string}>,
     *     searchBooleanColumns: array<int, string>
     * }
     */
    public static function catalogItems(): array
    {
        return [
            'searchOptions' => [
                ['value' => 'id', 'label' => __('app.catalog.admin.items.index.search_option_id')],
                ['value' => 'name', 'label' => __('app.catalog.admin.items.index.search_option_name')],
                ['value' => 'description', 'label' => __('app.catalog.admin.items.index.search_option_description')],
                ['value' => 'history', 'label' => __('app.catalog.admin.items.index.search_option_history')],
                ['value' => 'detalhes', 'label' => __('app.catalog.admin.items.index.search_option_detail')],
                ['value' => 'date', 'label' => __('app.catalog.admin.items.index.search_option_date')],
                [
                    'value' => 'identification_code',
                    'label' => __('app.catalog.admin.items.index.search_option_identification_code'),
                ],
                ['value' => 'validation', 'label' => __('app.catalog.admin.items.index.search_option_validation')],
                [
                    'value' => 'collaborator_id',
                    'label' => __('app.catalog.admin.items.index.search_option_collaborator'),
                ],
                ['value' => 'category_id', 'label' => __('app.catalog.admin.items.index.search_option_item_category')],
                ['value' => 'created_at', 'label' => __('app.catalog.admin.items.index.search_option_created_at')],
                ['value' => 'updated_at', 'label' => __('app.catalog.admin.items.index.search_option_updated_at')],
            ],
            'sortColumns' => [
                ['sort' => 'id', 'label' => __('app.catalog.admin.items.index.sort_id')],
                ['sort' => 'name', 'label' => __('app.catalog.admin.items.index.sort_name')],
                ['sort' => 'description', 'label' => __('app.catalog.admin.items.index.sort_description')],
                ['sort' => 'history', 'label' => __('app.catalog.admin.items.index.sort_history')],
                ['sort' => 'detail', 'label' => __('app.catalog.admin.items.index.sort_detail')],
                ['sort' => 'date', 'label' => __('app.catalog.admin.items.index.sort_date')],
                ['sort' => 'identification_code', 'label' => __('app.catalog.admin.items.index.sort_code')],
                ['sort' => 'validation', 'label' => __('app.catalog.admin.items.index.sort_validation')],
                ['sort' => 'category_id', 'label' => __('app.catalog.admin.items.index.sort_item_category')],
                ['sort' => 'collaborator_id', 'label' => __('app.catalog.admin.items.index.sort_collaborator')],
                ['sort' => 'created_at', 'label' => __('app.catalog.admin.items.index.sort_created_at')],
                ['sort' => 'updated_at', 'label' => __('app.catalog.admin.items.index.sort_updated_at')],
                ['sort' => null, 'label' => ''],
            ],
            'searchBooleanColumns' => self::booleanColumnsFromConfig(AdminIndexConfig::items()),
        ];
    }

    /**
     * @return array{
     *     searchOptions: list<array{value: string, label: string}>,
     *     sortColumns: list<array{sort: string|null, label: string}>,
     *     searchBooleanColumns: array<int, string>
     * }
     */
    public static function catalogItemTags(): array
    {
        $p = 'app.catalog.admin.item_tags.index';

        return [
            'searchOptions' => [
                ['value' => 'id', 'label' => __("$p.search_option_id")],
                ['value' => 'item_id', 'label' => __("$p.search_option_item_id")],
                ['value' => 'tag_id', 'label' => __("$p.search_option_tag_id")],
                ['value' => 'validation', 'label' => __("$p.search_option_validation")],
                ['value' => 'created_at', 'label' => __("$p.search_option_created_at")],
                ['value' => 'updated_at', 'label' => __("$p.search_option_updated_at")],
            ],
            'sortColumns' => [
                ['sort' => 'id', 'label' => __("$p.sort_id")],
                ['sort' => 'item_id', 'label' => __("$p.sort_item_id")],
                ['sort' => 'tag_id', 'label' => __("$p.sort_tag_id")],
                ['sort' => 'validation', 'label' => __("$p.sort_validation")],
                ['sort' => 'created_at', 'label' => __("$p.sort_created_at")],
                ['sort' => 'updated_at', 'label' => __("$p.sort_updated_at")],
                ['sort' => null, 'label' => ''],
            ],
            'searchBooleanColumns' => self::booleanColumnsFromConfig(AdminIndexConfig::itemTags()),
        ];
    }

    /**
     * @return array{
     *     searchOptions: list<array{value: string, label: string}>,
     *     sortColumns: list<array{sort: string|null, label: string}>,
     *     searchBooleanColumns: array<int, string>
     * }
     */
    public static function catalogItemComponents(): array
    {
        $p = 'app.catalog.admin.item_components.index';

        return [
            'searchOptions' => [
                ['value' => 'id', 'label' => __("$p.search_option_id")],
                ['value' => 'item_id', 'label' => __("$p.search_option_item_id")],
                ['value' => 'component_id', 'label' => __("$p.search_option_component_id")],
                ['value' => 'validation', 'label' => __("$p.search_option_validation")],
                ['value' => 'created_at', 'label' => __("$p.search_option_created_at")],
                ['value' => 'updated_at', 'label' => __("$p.search_option_updated_at")],
            ],
            'sortColumns' => [
                ['sort' => 'id', 'label' => __("$p.sort_id")],
                ['sort' => 'item_id', 'label' => __("$p.sort_item_id")],
                ['sort' => 'component_id', 'label' => __("$p.sort_component_id")],
                ['sort' => 'validation', 'label' => __("$p.sort_validation")],
                ['sort' => 'created_at', 'label' => __("$p.sort_created_at")],
                ['sort' => 'updated_at', 'label' => __("$p.sort_updated_at")],
                ['sort' => null, 'label' => ''],
            ],
            'searchBooleanColumns' => self::booleanColumnsFromConfig(
                AdminIndexConfig::itemComponents()
            ),
        ];
    }

    /**
     * @return array{
     *     searchOptions: list<array{value: string, label: string}>,
     *     sortColumns: list<array{sort: string|null, label: string}>,
     *     searchBooleanColumns: array<int, string>
     * }
     */
    public static function catalogExtras(): array
    {
        $p = 'app.catalog.admin.extras.index';

        return [
            'searchOptions' => [
                ['value' => 'id', 'label' => __("$p.search_option_id")],
                ['value' => 'info', 'label' => __("$p.search_option_info")],
                ['value' => 'item_id', 'label' => __("$p.search_option_item_id")],
                ['value' => 'collaborator_id', 'label' => __("$p.search_option_collaborator_id")],
                ['value' => 'validation', 'label' => __("$p.search_option_validation")],
                ['value' => 'created_at', 'label' => __("$p.search_option_created_at")],
                ['value' => 'updated_at', 'label' => __("$p.search_option_updated_at")],
            ],
            'sortColumns' => [
                ['sort' => 'id', 'label' => __("$p.sort_id")],
                ['sort' => 'info', 'label' => __("$p.sort_info")],
                ['sort' => 'item_id', 'label' => __("$p.sort_item_id")],
                ['sort' => 'collaborator_id', 'label' => __("$p.sort_collaborator_id")],
                ['sort' => 'validation', 'label' => __("$p.sort_validation")],
                ['sort' => 'created_at', 'label' => __("$p.sort_created_at")],
                ['sort' => 'updated_at', 'label' => __("$p.sort_updated_at")],
                ['sort' => null, 'label' => ''],
            ],
            'searchBooleanColumns' => self::booleanColumnsFromConfig(AdminIndexConfig::extras()),
        ];
    }

    /**
     * @return array{
     *     searchOptions: list<array{value: string, label: string}>,
     *     sortColumns: list<array{sort: string|null, label: string}>,
     *     searchBooleanColumns: array<int, string>
     * }
     */
    public static function catalogItemCategories(): array
    {
        return self::basicNameIndex(
            'app.catalog.admin.item_categories.index',
            AdminIndexConfig::itemCategories()
        );
    }

    /**
     * @return array{
     *     searchOptions: list<array{value: string, label: string}>,
     *     sortColumns: list<array{sort: string|null, label: string}>,
     *     searchBooleanColumns: array<int, string>
     * }
     */
    public static function identityAdmins(): array
    {
        $p = 'app.identity.admin.admins.index';

        return [
            'searchOptions' => [
                ['value' => 'id', 'label' => __("$p.search_option_id")],
                ['value' => 'username', 'label' => __("$p.search_option_username")],
                ['value' => 'created_at', 'label' => __("$p.search_option_created_at")],
                ['value' => 'updated_at', 'label' => __("$p.search_option_updated_at")],
            ],
            'sortColumns' => [
                ['sort' => 'id', 'label' => __("$p.sort_id")],
                ['sort' => 'username', 'label' => __("$p.sort_username")],
                ['sort' => 'created_at', 'label' => __("$p.sort_created_at")],
                ['sort' => 'updated_at', 'label' => __("$p.sort_updated_at")],
                ['sort' => null, 'label' => ''],
            ],
            'searchBooleanColumns' => self::booleanColumnsFromConfig(AdminIndexConfig::admins()),
        ];
    }

    /**
     * @return array{
     *     searchOptions: list<array{value: string, label: string}>,
     *     sortColumns: list<array{sort: string|null, label: string}>,
     *     searchBooleanColumns: array<int, string>
     * }
     */
    public static function collaborators(): array
    {
        $p = 'app.collaborator.admin.collaborators.index';

        return [
            'searchOptions' => [
                ['value' => 'id', 'label' => __("$p.search_option_id")],
                ['value' => 'full_name', 'label' => __("$p.search_option_full_name")],
                ['value' => 'contact', 'label' => __("$p.search_option_contact")],
                ['value' => 'role', 'label' => __("$p.search_option_role")],
                ['value' => 'blocked', 'label' => __("$p.search_option_blocked")],
                ['value' => 'created_at', 'label' => __("$p.search_option_created_at")],
                ['value' => 'updated_at', 'label' => __("$p.search_option_updated_at")],
            ],
            'sortColumns' => [
                ['sort' => 'id', 'label' => __("$p.sort_id")],
                ['sort' => 'full_name', 'label' => __("$p.sort_full_name")],
                ['sort' => 'contact', 'label' => __("$p.sort_contact")],
                ['sort' => 'role', 'label' => __("$p.sort_role")],
                ['sort' => 'blocked', 'label' => __("$p.sort_blocked")],
                ['sort' => 'created_at', 'label' => __("$p.sort_created_at")],
                ['sort' => 'updated_at', 'label' => __("$p.sort_updated_at")],
                ['sort' => null, 'label' => ''],
            ],
            'searchBooleanColumns' => self::booleanColumnsFromConfig(AdminIndexConfig::collaborators()),
        ];
    }

    /**
     * @return array{
     *     searchOptions: list<array{value: string, label: string}>,
     *     sortColumns: list<array{sort: string|null, label: string}>,
     *     searchBooleanColumns: array<int, string>
     * }
     */
    public static function taxonomyTags(): array
    {
        $p = 'app.taxonomy.admin.tags.index';

        return [
            'searchOptions' => [
                ['value' => 'id', 'label' => __("$p.search_option_id")],
                ['value' => 'name', 'label' => __("$p.search_option_name")],
                ['value' => 'validation', 'label' => __("$p.search_option_validation")],
                ['value' => 'tag_category_id', 'label' => __("$p.search_option_tag_category_id")],
                ['value' => 'created_at', 'label' => __("$p.search_option_created_at")],
                ['value' => 'updated_at', 'label' => __("$p.search_option_updated_at")],
            ],
            'sortColumns' => [
                ['sort' => 'id', 'label' => __("$p.sort_id")],
                ['sort' => 'name', 'label' => __("$p.sort_name")],
                ['sort' => 'validation', 'label' => __("$p.sort_validation")],
                ['sort' => 'tag_category_id', 'label' => __("$p.sort_tag_category_id")],
                ['sort' => 'created_at', 'label' => __("$p.sort_created_at")],
                ['sort' => 'updated_at', 'label' => __("$p.sort_updated_at")],
                ['sort' => null, 'label' => ''],
            ],
            'searchBooleanColumns' => self::booleanColumnsFromConfig(AdminIndexConfig::tags()),
        ];
    }

    /**
     * @return array{
     *     searchOptions: list<array{value: string, label: string}>,
     *     sortColumns: list<array{sort: string|null, label: string}>,
     *     searchBooleanColumns: array<int, string>
     * }
     */
    public static function taxonomyTagCategories(): array
    {
        return self::basicNameIndex(
            'app.taxonomy.admin.tag_categories.index',
            AdminIndexConfig::tagCategories()
        );
    }
}
