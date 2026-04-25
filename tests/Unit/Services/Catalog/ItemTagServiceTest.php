<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Catalog;

use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Models\Language;
use App\Models\Location;
use App\Models\Taxonomy\Tag;
use App\Models\Taxonomy\TagCategory;
use App\Services\Catalog\ItemTagService;
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
        ]);

        $ids = $item->tags()->pluck('tags.id')->sort()->values()->all();
        $this->assertSame([$tagA->id, $tagB->id], $ids);
    }

    public function test_attach_tags_to_item_with_explicit_content_language_id(): void
    {
        $tagCategory = new TagCategory();
        $tagCategory->save();
        $tagCategory->syncPrimaryLocaleTranslation(['name' => 'CatSvc2_' . uniqid('', false)]);

        $tagA = Tag::create(['tag_category_id' => $tagCategory->id, 'validation' => true]);
        $tagA->syncPrimaryLocaleTranslation(['name' => 'TagA2_' . uniqid('', false)]);
        $tagB = Tag::create(['tag_category_id' => $tagCategory->id, 'validation' => true]);
        $tagB->syncPrimaryLocaleTranslation(['name' => 'TagB2_' . uniqid('', false)]);

        $categoryId = ItemCategoryFactory::new()->create()->id;
        $locationId = Location::factory()->create()->id;
        $collaboratorId = Collaborator::factory()->create()->id;
        $item = Item::factory()->create([
            'category_id' => $categoryId,
            'location_id' => $locationId,
            'collaborator_id' => $collaboratorId,
        ]);

        $contentLanguageId = Language::idForCode('pt_BR');

        $svc = app(ItemTagService::class);
        $svc->attachTagsToItem($item, [
            ['tag_category_id' => $tagCategory->id, 'name' => $tagA->name],
            ['tag_category_id' => $tagCategory->id, 'name' => $tagB->name],
        ], $contentLanguageId);

        $ids = $item->tags()->pluck('tags.id')->sort()->values()->all();
        $this->assertSame([$tagA->id, $tagB->id], $ids);
    }
}
