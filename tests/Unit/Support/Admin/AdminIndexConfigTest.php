<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Admin;

use App\Support\Admin\AdminIndexConfig;
use Tests\TestCase;

final class AdminIndexConfigTest extends TestCase
{
    public function test_each_config_has_expected_base_table(): void
    {
        $this->assertSame('extras', AdminIndexConfig::extras()['baseTable']);
        $this->assertSame('items', AdminIndexConfig::items()['baseTable']);
        $this->assertSame('collaborators', AdminIndexConfig::collaborators()['baseTable']);
        $this->assertSame('admins', AdminIndexConfig::admins()['baseTable']);
        $this->assertSame('item_categories', AdminIndexConfig::itemCategories()['baseTable']);
        $this->assertSame('tag_categories', AdminIndexConfig::tagCategories()['baseTable']);
        $this->assertSame('item_component', AdminIndexConfig::itemComponents()['baseTable']);
        $this->assertSame('item_tag', AdminIndexConfig::itemTags()['baseTable']);
        $this->assertSame('tags', AdminIndexConfig::tags()['baseTable']);
    }

    public function test_items_config_includes_boolean_and_numeric_exact_columns(): void
    {
        /** @var array<string, mixed> $items */
        $items = AdminIndexConfig::items();

        $this->assertContains('validation', $items['booleanColumns']);
        $this->assertArrayHasKey('exactColumnsNumeric', $items);
        $this->assertContains('location_id', $items['exactColumnsNumeric']);
    }
}
