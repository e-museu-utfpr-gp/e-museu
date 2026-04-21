<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Admin;

use App\Models\Catalog\ItemCategory;
use App\Support\Admin\AdminIndexConfig;
use App\Support\Admin\AdminIndexQueryBuilder;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('mysql')]
final class AdminIndexQueryBuilderTest extends ServiceMysqlTestCase
{
    public function test_build_applies_name_search_like_on_item_categories(): void
    {
        ItemCategory::factory()->create();
        $request = Request::create('/', 'GET', [
            'search_column' => 'name',
            'search' => 'a',
            'sort' => 'name',
            'order' => 'asc',
        ]);
        $query = ItemCategory::query();
        AdminIndexQueryBuilder::build($query, $request, AdminIndexConfig::itemCategories());

        $sql = strtolower($query->toSql());
        $this->assertStringContainsString('like', $sql);
    }
}
