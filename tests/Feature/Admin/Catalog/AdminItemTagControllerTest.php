<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Catalog;

use App\Models\Catalog\Item;
use App\Models\Catalog\ItemTag;
use App\Models\Collaborator\Collaborator;
use App\Models\Identity\Admin;
use App\Models\Location;
use App\Models\Taxonomy\Tag;
use App\Models\Language;
use App\Models\Taxonomy\TagCategory;
use Database\Factories\Catalog\ItemCategoryFactory;
use Database\Factories\Taxonomy\TagFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
class AdminItemTagControllerTest extends AbstractMysqlRefreshDatabaseTestCase
{
    public function test_guest_is_redirected_from_item_tags_index_to_login(): void
    {
        $this->get(route('admin.catalog.item-tags.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_item_tags_create_to_login(): void
    {
        $this->get(route('admin.catalog.item-tags.create'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_item_tags_show_to_login(): void
    {
        $itemTag = $this->createItemTagWithFixtures();

        $this->get(route('admin.catalog.item-tags.show', $itemTag))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_store_item_tag(): void
    {
        $this->get(route('login'));

        $this->post(route('admin.catalog.item-tags.store'), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_update_item_tag(): void
    {
        $itemTag = $this->createItemTagWithFixtures();
        $this->get(route('login'));

        $this->put(route('admin.catalog.item-tags.update', $itemTag), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_destroy_item_tag(): void
    {
        $itemTag = $this->createItemTagWithFixtures();
        $this->get(route('login'));

        $this->delete(route('admin.catalog.item-tags.destroy', $itemTag), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_tags_by_category_to_login(): void
    {
        $this->get(route('admin.catalog.tags.by-category', ['category' => '1']))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_view_item_tags_index(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.catalog.item-tags.index'))
            ->assertOk()
            ->assertViewIs('pages.admin.catalog.item-tags.index')
            ->assertViewHas([
                'itemTags',
                'count',
                'searchOptions',
                'sortColumns',
                'searchBooleanColumns',
            ]);
    }

    public function test_admin_can_view_item_tags_create_form(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.catalog.item-tags.create'))
            ->assertOk()
            ->assertViewIs('pages.admin.catalog.item-tags.create')
            ->assertViewHas(['itemCategories', 'categories']);
    }

    public function test_admin_can_view_item_tag_show(): void
    {
        $itemTag = $this->createItemTagWithFixtures();

        $this->actingAs($this->createAdmin());

        $this->get(route('admin.catalog.item-tags.show', $itemTag))
            ->assertOk()
            ->assertViewIs('pages.admin.catalog.item-tags.show')
            ->assertViewHas('itemTag');
    }

    public function test_admin_can_store_item_tag_and_redirect_to_show(): void
    {
        $item = $this->createItemWithFixtures();
        $tag = $this->createTagNotSharingItemPrimaryKey($item);

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.catalog.item-tags.create'));

        $response = $this->post(route('admin.catalog.item-tags.store'), [
            '_token' => session()->token(),
            'item_id' => $item->id,
            'tag_id' => $tag->id,
            'validation' => 1,
        ]);

        $itemTag = ItemTag::query()
            ->where('item_id', $item->id)
            ->where('tag_id', $tag->id)
            ->first();
        $this->assertNotNull($itemTag);

        $response->assertRedirect(route('admin.catalog.item-tags.show', $itemTag));
        $response->assertSessionHas('success');
        $this->assertTrue($itemTag->validation);
    }

    public function test_store_redirects_with_errors_when_payload_invalid(): void
    {
        $this->actingAs($this->createAdmin());
        $this->from(route('admin.catalog.item-tags.create'));

        $this->post(route('admin.catalog.item-tags.store'), [
            '_token' => session()->token(),
        ])
            ->assertRedirect(route('admin.catalog.item-tags.create'))
            ->assertSessionHasErrors(['item_id', 'tag_id', 'validation']);
    }

    public function test_store_rejects_unknown_item_id(): void
    {
        $item = $this->createItemWithFixtures();
        $tag = $this->createTagNotSharingItemPrimaryKey($item);

        $this->actingAs($this->createAdmin());
        $this->from(route('admin.catalog.item-tags.create'));

        $this->post(route('admin.catalog.item-tags.store'), [
            '_token' => session()->token(),
            'item_id' => 9_999_999,
            'tag_id' => $tag->id,
            'validation' => 1,
        ])
            ->assertRedirect(route('admin.catalog.item-tags.create'))
            ->assertSessionHasErrors('item_id');
    }

    public function test_show_returns_not_found_for_unknown_item_tag_id(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.catalog.item-tags.show', ['item_tag' => 9_999_999]))
            ->assertNotFound();
    }

    public function test_store_rejects_when_tag_id_equals_item_id(): void
    {
        $item = $this->createItemWithFixtures();
        $categoryId = TagCategory::query()->orderBy('id')->value('id');
        $this->assertNotNull($categoryId);

        $this->assertDatabaseMissing('tags', ['id' => $item->id]);

        $now = now();
        DB::table('tags')->insert([
            'id' => $item->id,
            'tag_category_id' => $categoryId,
            'validation' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('tag_translations')->insert([
            'tag_id' => $item->id,
            'language_id' => Language::idForPreferredFormLocale(),
            'name' => 'Tag row forced to share items.id for collision test',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $maxTagId = (int) DB::table('tags')->max('id');
        DB::statement('ALTER TABLE tags AUTO_INCREMENT = ' . ($maxTagId + 1));

        $this->assertTrue(Tag::query()->whereKey($item->id)->exists());

        $this->actingAs($this->createAdmin());
        $this->from(route('admin.catalog.item-tags.create'));

        $this->post(route('admin.catalog.item-tags.store'), [
            '_token' => session()->token(),
            'item_id' => $item->id,
            'tag_id' => $item->id,
            'validation' => 1,
        ])
            ->assertRedirect(route('admin.catalog.item-tags.create'))
            ->assertSessionHasErrors('tag_id');
    }

    public function test_admin_can_toggle_item_tag_validation_via_update(): void
    {
        $itemTag = $this->createItemTagWithFixtures(['validation' => true]);

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.catalog.item-tags.show', $itemTag));

        $response = $this->put(route('admin.catalog.item-tags.update', $itemTag), [
            '_token' => session()->token(),
        ]);

        $response->assertRedirect(route('admin.catalog.item-tags.show', $itemTag));
        $response->assertSessionHas('success');

        $itemTag->refresh();
        $this->assertFalse($itemTag->validation);
    }

    public function test_admin_second_update_toggles_validation_back_to_true(): void
    {
        $itemTag = $this->createItemTagWithFixtures(['validation' => true]);

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.catalog.item-tags.show', $itemTag));

        $this->put(route('admin.catalog.item-tags.update', $itemTag), [
            '_token' => session()->token(),
        ]);
        $itemTag->refresh();
        $this->assertFalse($itemTag->validation);

        $this->get(route('admin.catalog.item-tags.show', $itemTag));
        $this->put(route('admin.catalog.item-tags.update', $itemTag), [
            '_token' => session()->token(),
        ]);

        $itemTag->refresh();
        $this->assertTrue($itemTag->validation);
    }

    public function test_admin_can_destroy_item_tag(): void
    {
        $itemTag = $this->createItemTagWithFixtures();

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.catalog.item-tags.index'));

        $response = $this->delete(route('admin.catalog.item-tags.destroy', $itemTag), [
            '_token' => session()->token(),
        ]);

        $response->assertRedirect(route('admin.catalog.item-tags.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('item_tag', ['id' => $itemTag->id]);
    }

    public function test_tags_by_category_returns_json_payload_for_admin(): void
    {
        $category = TagCategory::query()->orderBy('id')->firstOrFail();
        $tag = TagFactory::new()->create([
            'tag_category_id' => $category->id,
            'validation' => true,
        ]);
        $tag->syncPrimaryLocaleTranslation(['name' => 'AdminItemTagByCat']);

        $this->actingAs($this->createAdmin());

        $this->getJson(route('admin.catalog.tags.by-category', ['category' => (string) $category->id]))
            ->assertOk()
            ->assertJsonFragment(['id' => $tag->id, 'name' => 'AdminItemTagByCat']);
    }

    public function test_tags_by_category_includes_unvalidated_tags_for_admin(): void
    {
        $category = TagCategory::query()->orderBy('id')->firstOrFail();
        $draft = TagFactory::new()->create([
            'tag_category_id' => $category->id,
            'validation' => false,
        ]);
        $draft->syncPrimaryLocaleTranslation(['name' => 'AdminItemTagDraftOnly']);

        $this->actingAs($this->createAdmin());

        $this->getJson(route('admin.catalog.tags.by-category', ['category' => (string) $category->id]))
            ->assertOk()
            ->assertJsonFragment(['id' => $draft->id, 'name' => 'AdminItemTagDraftOnly']);
    }

    public function test_tags_by_category_returns_empty_data_when_category_missing(): void
    {
        $this->actingAs($this->createAdmin());

        $this->getJson(route('admin.catalog.tags.by-category', ['category' => '']))
            ->assertOk()
            ->assertExactJson([
                'data' => [],
                'meta' => [
                    'total' => 0,
                    'returned' => 0,
                    'truncated' => false,
                ],
            ]);
    }

    private function createAdmin(): Admin
    {
        return Admin::create([
            'username' => 'item_tag_ctrl_' . uniqid('', false),
            'password' => Hash::make('secret'),
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides  Item attribute overrides (e.g. category_id).
     */
    private function createItemWithFixtures(array $overrides = []): Item
    {
        $categoryId = $overrides['category_id'] ?? ItemCategoryFactory::new()->create()->id;
        $locationId = $overrides['location_id'] ?? Location::factory()->create()->id;
        $collaboratorId = $overrides['collaborator_id'] ?? Collaborator::factory()->create()->id;

        return Item::factory()->create(array_merge([
            'category_id' => $categoryId,
            'location_id' => $locationId,
            'collaborator_id' => $collaboratorId,
        ], $overrides));
    }

    /**
     * @param  array{item_id?: int, tag_id?: int, validation?: bool}  $overrides
     */
    private function createItemTagWithFixtures(array $overrides = []): ItemTag
    {
        $item = isset($overrides['item_id'])
            ? Item::query()->findOrFail($overrides['item_id'])
            : $this->createItemWithFixtures();
        $tag = isset($overrides['tag_id'])
            ? Tag::query()->findOrFail($overrides['tag_id'])
            : $this->createTagNotSharingItemPrimaryKey($item);

        return ItemTag::query()->create([
            'item_id' => $item->id,
            'tag_id' => $tag->id,
            'validation' => $overrides['validation'] ?? true,
        ]);
    }

    private function createTagNotSharingItemPrimaryKey(Item $item): Tag
    {
        $categoryId = TagCategory::query()->orderBy('id')->value('id');
        $this->assertNotNull($categoryId);

        $attempts = 0;
        $tag = null;
        do {
            $created = TagFactory::new()->create([
                'tag_category_id' => $categoryId,
                'validation' => true,
            ]);
            $this->assertInstanceOf(Tag::class, $created);
            $tag = $created;
            $attempts++;
        } while ((int) $tag->id === (int) $item->id && $attempts < 40);

        $this->assertNotSame(
            (int) $item->id,
            (int) $tag->id,
            'Could not create a tag whose id differs from the item id for relation rules.'
        );

        $this->assertInstanceOf(Tag::class, $tag);

        return $tag;
    }
}
