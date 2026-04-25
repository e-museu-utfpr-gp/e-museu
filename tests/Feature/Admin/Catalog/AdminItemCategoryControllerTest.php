<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Catalog;

use App\Models\Catalog\ItemCategory;
use App\Models\Identity\Admin;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
class AdminItemCategoryControllerTest extends AbstractMysqlRefreshDatabaseTestCase
{
    public function test_guest_is_redirected_from_item_categories_index_to_login(): void
    {
        $this->get(route('admin.catalog.item-categories.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_item_categories_create_to_login(): void
    {
        $this->get(route('admin.catalog.item-categories.create'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_item_categories_show_to_login(): void
    {
        $category = $this->createItemCategoryWithName('GuestBlockShow');

        $this->get(route('admin.catalog.item-categories.show', $category))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_item_categories_edit_to_login(): void
    {
        $category = $this->createItemCategoryWithName('GuestBlockEdit');

        $this->get(route('admin.catalog.item-categories.edit', $category))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_store_item_category(): void
    {
        $this->get(route('login'));

        $this->post(route('admin.catalog.item-categories.store'), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_update_item_category(): void
    {
        $category = $this->createItemCategoryWithName('GuestBlockUpdate');
        $this->get(route('login'));

        $this->put(route('admin.catalog.item-categories.update', $category), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_destroy_item_category(): void
    {
        $category = $this->createItemCategoryWithName('GuestBlockDestroy');
        $this->get(route('login'));

        $this->delete(route('admin.catalog.item-categories.destroy', $category), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_admin_can_view_item_categories_index(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.catalog.item-categories.index'))
            ->assertOk()
            ->assertViewIs('pages.admin.catalog.item-categories.index')
            ->assertViewHas([
                'itemCategories',
                'count',
                'searchOptions',
                'sortColumns',
                'searchBooleanColumns',
            ]);
    }

    public function test_admin_index_lists_item_category_name(): void
    {
        $needle = 'ItemCatCtrlIx_' . uniqid('', false);
        $this->createItemCategoryWithName($needle);

        $this->actingAs($this->createAdmin());

        $this->get(route('admin.catalog.item-categories.index'))
            ->assertOk()
            ->assertSee($needle, false);
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.catalog.item-categories.create'))
            ->assertOk()
            ->assertViewIs('pages.admin.catalog.item-categories.create')
            ->assertViewHas(['contentLanguages', 'preferredContentTabLanguageId']);
    }

    public function test_admin_can_store_item_category_and_redirect_to_show(): void
    {
        $name = 'ItemCatStore_' . uniqid('', false);

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.catalog.item-categories.create'));

        $response = $this->post(route('admin.catalog.item-categories.store'), [
            '_token' => session()->token(),
            'translations' => $this->minimalTranslationsPayload($name),
        ]);

        $category = ItemCategory::query()->latest('id')->first();
        $this->assertNotNull($category);

        $response->assertRedirect(route('admin.catalog.item-categories.show', $category));
        $response->assertSessionHas('success');
        $category->load('translations');
        $this->assertTrue(
            $category->translations->contains(fn ($t): bool => $t->name === $name)
        );
    }

    public function test_store_redirects_with_errors_when_translations_invalid(): void
    {
        $this->actingAs($this->createAdmin());
        $this->from(route('admin.catalog.item-categories.create'));

        $this->post(route('admin.catalog.item-categories.store'), [
            '_token' => session()->token(),
            'translations' => [
                'universal' => [],
                'pt_BR' => ['name' => ''],
                'en' => [],
            ],
        ])
            ->assertRedirect(route('admin.catalog.item-categories.create'))
            ->assertSessionHasErrors('translations');
    }

    public function test_admin_can_view_show(): void
    {
        $category = $this->createItemCategoryWithName('ItemCatShowView');

        $this->actingAs($this->createAdmin());

        $this->get(route('admin.catalog.item-categories.show', $category))
            ->assertOk()
            ->assertViewIs('pages.admin.catalog.item-categories.show')
            ->assertViewHas('itemCategory');
    }

    public function test_show_returns_not_found_for_unknown_item_category_id(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.catalog.item-categories.show', ['item_category' => 9_999_999]))
            ->assertNotFound();
    }

    public function test_admin_can_open_edit_form(): void
    {
        $category = $this->createItemCategoryWithName('ItemCatEditForm');

        $this->actingAs($this->createAdmin());

        $this->get(route('admin.catalog.item-categories.edit', $category))
            ->assertOk()
            ->assertViewIs('pages.admin.catalog.item-categories.edit')
            ->assertViewHas(['itemCategory', 'contentLanguages']);
    }

    public function test_admin_can_update_item_category_and_redirect_to_show(): void
    {
        $category = $this->createItemCategoryWithName('BeforeItemCatUpdate');

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.catalog.item-categories.edit', $category));

        $updated = 'AfterItemCatUpdate_' . uniqid('', false);
        $response = $this->put(route('admin.catalog.item-categories.update', $category), [
            '_token' => session()->token(),
            'translations' => $this->minimalTranslationsPayload($updated),
        ]);

        $response->assertRedirect(route('admin.catalog.item-categories.show', $category));
        $response->assertSessionHas('success');

        $category->refresh();
        $category->load('translations');
        $this->assertTrue(
            $category->translations->contains(fn ($t): bool => $t->name === $updated)
        );
    }

    public function test_admin_can_destroy_item_category_without_items(): void
    {
        $name = 'ItemCatDestroy_' . uniqid('', false);

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.catalog.item-categories.create'));

        $this->post(route('admin.catalog.item-categories.store'), [
            '_token' => session()->token(),
            'translations' => $this->minimalTranslationsPayload($name),
        ]);

        $category = ItemCategory::query()->latest('id')->first();
        $this->assertNotNull($category);

        $this->get(route('admin.catalog.item-categories.index'));

        $response = $this->delete(route('admin.catalog.item-categories.destroy', $category), [
            '_token' => session()->token(),
        ]);

        $response->assertRedirect(route('admin.catalog.item-categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('item_categories', ['id' => $category->id]);
    }

    private function createAdmin(): Admin
    {
        return Admin::create([
            'username' => 'item_cat_ctrl_' . uniqid('', false),
            'password' => Hash::make('secret'),
        ]);
    }

    private function createItemCategoryWithName(string $name): ItemCategory
    {
        $category = new ItemCategory();
        $category->save();
        $category->syncPrimaryLocaleTranslation(['name' => $name]);

        return $category;
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function minimalTranslationsPayload(string $ptBrName): array
    {
        return [
            'universal' => [],
            'pt_BR' => [
                'name' => $ptBrName,
            ],
            'en' => [],
        ];
    }
}
