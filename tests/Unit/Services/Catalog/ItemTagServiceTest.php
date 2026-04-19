<?php

namespace Tests\Unit\Services\Catalog;

use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Models\Location;
use App\Models\Taxonomy\Tag;
use App\Models\Taxonomy\TagCategory;
use App\Services\Catalog\ItemTagService;
use App\Services\Taxonomy\TagService;
use Database\Factories\Catalog\ItemCategoryFactory;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('services')]
class ItemTagServiceTest extends ServiceMysqlTestCase
{
    public function test_attach_tags_to_item_syncs_unique_tag_ids(): void
    {
        $tagCategory = new TagCategory();
        $tagCategory->save();
        $tagCategory->syncPrimaryLocaleTranslation(['name' => 'CatSvc_' . uniqid('', false)]);

        $tagA = Tag::create(['tag_category_id' => $tagCategory->id, 'validation' => true]);
        $tagA->syncPrimaryLocaleTranslation(['name' => 'TagA_' . uniqid('', false)]);
        $tagB = Tag::create(['tag_category_id' => $tagCategory->id, 'validation' => true]);
        $tagB->syncPrimaryLocaleTranslation(['name' => 'TagB_' . uniqid('', false)]);

        $categoryId = ItemCategoryFactory::new()->create()->id;
        $locationId = Location::factory()->create()->id;
        $collaboratorId = Collaborator::factory()->create()->id;
        $item = Item::factory()->create([
            'category_id' => $categoryId,
            'location_id' => $locationId,
            'collaborator_id' => $collaboratorId,
        ]);

        $svc = app(ItemTagService::class);
        $svc->attachTagsToItem($item, [
            ['tag_category_id' => $tagCategory->id, 'name' => $tagA->name],
            ['tag_category_id' => $tagCategory->id, 'name' => $tagB->name],
        ], app(TagService::class));

        $ids = $item->tags()->pluck('tags.id')->sort()->values()->all();
        $this->assertSame([$tagA->id, $tagB->id], $ids);
    }
}
