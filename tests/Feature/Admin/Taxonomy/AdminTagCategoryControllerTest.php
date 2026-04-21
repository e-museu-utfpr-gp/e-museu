<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Taxonomy;

use App\Models\Identity\Admin;
use App\Models\Taxonomy\TagCategory;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
class AdminTagCategoryControllerTest extends AbstractMysqlRefreshDatabaseTestCase
{
    public function test_guest_is_redirected_from_tag_categories_index_to_login(): void
    {
        $this->get(route('admin.taxonomy.tag-categories.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_tag_categories_create_to_login(): void
    {
        $this->get(route('admin.taxonomy.tag-categories.create'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_tag_categories_show_to_login(): void
    {
        $category = $this->createTagCategoryWithName('GuestBlockShow');

        $this->get(route('admin.taxonomy.tag-categories.show', $category))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_tag_categories_edit_to_login(): void
    {
        $category = $this->createTagCategoryWithName('GuestBlockEdit');

        $this->get(route('admin.taxonomy.tag-categories.edit', $category))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_store_tag_category(): void
    {
        $this->get(route('login'));

        $this->post(route('admin.taxonomy.tag-categories.store'), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_update_tag_category(): void
    {
        $category = $this->createTagCategoryWithName('GuestBlockUpdate');
        $this->get(route('login'));

        $this->put(route('admin.taxonomy.tag-categories.update', $category), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_destroy_tag_category(): void
    {
        $category = $this->createTagCategoryWithName('GuestBlockDestroy');
        $this->get(route('login'));

        $this->delete(route('admin.taxonomy.tag-categories.destroy', $category), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_admin_can_view_tag_categories_index(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.taxonomy.tag-categories.index'))
            ->assertOk()
            ->assertViewIs('pages.admin.taxonomy.tag-categories.index')
            ->assertViewHas([
                'tagCategories',
                'count',
                'searchOptions',
                'sortColumns',
                'searchBooleanColumns',
            ]);
    }

    public function test_admin_index_lists_tag_category_name(): void
    {
        $needle = 'TagCatCtrlIx_' . uniqid('', false);
        $this->createTagCategoryWithName($needle);

        $this->actingAs($this->createAdmin());

        $this->get(route('admin.taxonomy.tag-categories.index'))
            ->assertOk()
            ->assertSee($needle, false);
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.taxonomy.tag-categories.create'))
            ->assertOk()
            ->assertViewIs('pages.admin.taxonomy.tag-categories.create')
            ->assertViewHas(['contentLanguages', 'preferredContentTabLanguageId']);
    }

    public function test_admin_can_store_tag_category_and_redirect_to_show(): void
    {
        $name = 'TagCatStore_' . uniqid('', false);

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.taxonomy.tag-categories.create'));

        $response = $this->post(route('admin.taxonomy.tag-categories.store'), [
            '_token' => session()->token(),
            'translations' => $this->minimalTranslationsPayload($name),
        ]);

        $category = TagCategory::query()->latest('id')->first();
        $this->assertNotNull($category);

        $response->assertRedirect(route('admin.taxonomy.tag-categories.show', $category));
        $response->assertSessionHas('success');
        $category->load('translations');
        $this->assertTrue(
            $category->translations->contains(fn ($t): bool => $t->name === $name)
        );
    }

    public function test_store_redirects_with_errors_when_translations_invalid(): void
    {
        $this->actingAs($this->createAdmin());
        $this->from(route('admin.taxonomy.tag-categories.create'));

        $this->post(route('admin.taxonomy.tag-categories.store'), [
            '_token' => session()->token(),
            'translations' => [
                'universal' => [],
                'pt_BR' => ['name' => ''],
                'en' => [],
            ],
        ])
            ->assertRedirect(route('admin.taxonomy.tag-categories.create'))
            ->assertSessionHasErrors('translations');
    }

    public function test_admin_can_view_show(): void
    {
        $category = $this->createTagCategoryWithName('TagCatShowView');

        $this->actingAs($this->createAdmin());

        $this->get(route('admin.taxonomy.tag-categories.show', $category))
            ->assertOk()
            ->assertViewIs('pages.admin.taxonomy.tag-categories.show')
            ->assertViewHas('tagCategory');
    }

    public function test_show_returns_not_found_for_unknown_tag_category_id(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.taxonomy.tag-categories.show', ['tag_category' => 9_999_999]))
            ->assertNotFound();
    }

    public function test_admin_can_open_edit_form(): void
    {
        $category = $this->createTagCategoryWithName('TagCatEditForm');

        $this->actingAs($this->createAdmin());

        $this->get(route('admin.taxonomy.tag-categories.edit', $category))
            ->assertOk()
            ->assertViewIs('pages.admin.taxonomy.tag-categories.edit')
            ->assertViewHas(['tagCategory', 'contentLanguages']);
    }

    public function test_admin_can_update_tag_category_and_redirect_to_show(): void
    {
        $category = $this->createTagCategoryWithName('BeforeTagCatUpdate');

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.taxonomy.tag-categories.edit', $category));

        $updated = 'AfterTagCatUpdate_' . uniqid('', false);
        $response = $this->put(route('admin.taxonomy.tag-categories.update', $category), [
            '_token' => session()->token(),
            'translations' => $this->minimalTranslationsPayload($updated),
        ]);

        $response->assertRedirect(route('admin.taxonomy.tag-categories.show', $category));
        $response->assertSessionHas('success');

        $category->refresh();
        $category->load('translations');
        $this->assertTrue(
            $category->translations->contains(fn ($t): bool => $t->name === $updated)
        );
    }

    public function test_admin_can_destroy_tag_category_without_tags(): void
    {
        $name = 'TagCatDestroy_' . uniqid('', false);

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.taxonomy.tag-categories.create'));

        $this->post(route('admin.taxonomy.tag-categories.store'), [
            '_token' => session()->token(),
            'translations' => $this->minimalTranslationsPayload($name),
        ]);

        $category = TagCategory::query()->latest('id')->first();
        $this->assertNotNull($category);

        $this->get(route('admin.taxonomy.tag-categories.index'));

        $response = $this->delete(route('admin.taxonomy.tag-categories.destroy', $category), [
            '_token' => session()->token(),
        ]);

        $response->assertRedirect(route('admin.taxonomy.tag-categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('tag_categories', ['id' => $category->id]);
    }

    private function createAdmin(): Admin
    {
        return Admin::create([
            'username' => 'tag_cat_ctrl_' . uniqid('', false),
            'password' => Hash::make('secret'),
        ]);
    }

    private function createTagCategoryWithName(string $name): TagCategory
    {
        $category = new TagCategory();
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
