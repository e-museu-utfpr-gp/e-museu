<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Admin;

use App\Support\Admin\AdminIndexTableView;
use Tests\TestCase;

final class AdminIndexTableViewTest extends TestCase
{
    /**
     * @return list<callable(): array<string, mixed>>
     */
    private static function viewFactories(): array
    {
        return [
            static fn (): array => AdminIndexTableView::catalogItems(),
            static fn (): array => AdminIndexTableView::catalogItemTags(),
            static fn (): array => AdminIndexTableView::catalogItemComponents(),
            static fn (): array => AdminIndexTableView::catalogExtras(),
            static fn (): array => AdminIndexTableView::catalogItemCategories(),
            static fn (): array => AdminIndexTableView::identityAdmins(),
            static fn (): array => AdminIndexTableView::collaborators(),
            static fn (): array => AdminIndexTableView::taxonomyTags(),
            static fn (): array => AdminIndexTableView::taxonomyTagCategories(),
        ];
    }

    public function test_each_public_view_has_index_shape(): void
    {
        foreach (self::viewFactories() as $factory) {
            $view = $factory();
            $this->assertArrayHasKey('searchOptions', $view);
            $this->assertArrayHasKey('sortColumns', $view);
            $this->assertArrayHasKey('searchBooleanColumns', $view);
            $this->assertNotEmpty($view['searchOptions']);
            $this->assertNotEmpty($view['sortColumns']);
            $lastSort = $view['sortColumns'][array_key_last($view['sortColumns'])];
            $this->assertNull($lastSort['sort']);
        }
    }

    public function test_catalog_items_first_search_option_is_id(): void
    {
        $view = AdminIndexTableView::catalogItems();

        $this->assertSame('id', $view['searchOptions'][0]['value']);
    }
}
