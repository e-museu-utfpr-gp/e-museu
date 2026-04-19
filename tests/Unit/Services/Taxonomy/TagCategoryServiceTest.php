<?php

namespace Tests\Unit\Services\Taxonomy;

use App\Models\Taxonomy\TagCategory;
use App\Services\Taxonomy\TagCategoryService;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('services')]
class TagCategoryServiceTest extends ServiceMysqlTestCase
{
    public function test_delete_tag_category_removes_row(): void
    {
        $category = new TagCategory();
        $category->save();
        $category->syncPrimaryLocaleTranslation(['name' => 'DelCat_' . uniqid('', false)]);

        $svc = app(TagCategoryService::class);
        $svc->deleteTagCategory($category);

        $this->assertDatabaseMissing('tag_categories', ['id' => $category->id]);
    }
}
