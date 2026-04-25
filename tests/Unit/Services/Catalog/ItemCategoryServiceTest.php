<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Catalog;

use App\Models\Catalog\ItemCategory;
use App\Services\Catalog\ItemCategoryService;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('services')]
class ItemCategoryServiceTest extends ServiceMysqlTestCase
{
    public function test_delete_item_category_removes_row(): void
    {
        $category = new ItemCategory();
        $category->save();
        $category->syncPrimaryLocaleTranslation(['name' => 'DelItemCat_' . uniqid('', false)]);

        $svc = app(ItemCategoryService::class);
        $svc->deleteItemCategory($category);

        $this->assertDatabaseMissing('item_categories', ['id' => $category->id]);
    }
}
