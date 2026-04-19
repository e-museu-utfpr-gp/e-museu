<?php

namespace Tests\Feature\Admin\Taxonomy;

use App\Models\Identity\Admin;
use App\Models\Taxonomy\Tag;
use App\Models\Taxonomy\TagCategory;
use Database\Factories\Taxonomy\TagFactory;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
class AdminTagControllerTest extends AbstractMysqlRefreshDatabaseTestCase
{
    public function test_guest_is_redirected_from_tags_index_to_login(): void
    {
        $this->get(route('admin.taxonomy.tags.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_tags_create_to_login(): void
    {
        $this->get(route('admin.taxonomy.tags.create'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_tags_show_to_login(): void
    {
        $tag = $this->createTagWithName('GuestBlockTagShow');

        $this->get(route('admin.taxonomy.tags.show', $tag))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_tags_edit_to_login(): void
    {
        $tag = $this->createTagWithName('GuestBlockTagEdit');

        $this->get(route('admin.taxonomy.tags.edit', $tag))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_store_tag(): void
    {
        $this->get(route('login'));

        $this->post(route('admin.taxonomy.tags.store'), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_update_tag(): void
    {
        $tag = $this->createTagWithName('GuestBlockTagUpdate');
        $this->get(route('login'));

        $this->put(route('admin.taxonomy.tags.update', $tag), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_destroy_tag(): void
    {
        $tag = $this->createTagWithName('GuestBlockTagDestroy');
        $this->get(route('login'));

        $this->delete(route('admin.taxonomy.tags.destroy', $tag), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_admin_can_view_tags_index(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.taxonomy.tags.index'))
            ->assertOk()
            ->assertViewIs('pages.admin.taxonomy.tags.index')
            ->assertViewHas([
                'tags',
                'count',
                'searchOptions',
                'sortColumns',
                'searchBooleanColumns',
            ]);
    }

    public function test_admin_index_lists_tag_name(): void
    {
        $needle = 'AdminTagCtrlIx_' . uniqid('', false);
        $this->createTagWithName($needle);

        $this->actingAs($this->createAdmin());

        $this->get(route('admin.taxonomy.tags.index'))
            ->assertOk()
            ->assertSee($needle, false);
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.taxonomy.tags.create'))
            ->assertOk()
            ->assertViewIs('pages.admin.taxonomy.tags.create')
            ->assertViewHas(['categories', 'contentLanguages', 'preferredContentTabLanguageId']);
    }

    public function test_admin_can_store_tag_and_redirect_to_show(): void
    {
        $categoryId = $this->tagCategoryId();
        $name = 'AdminTagStore_' . uniqid('', false);

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.taxonomy.tags.create'));

        $response = $this->post(route('admin.taxonomy.tags.store'), [
            '_token' => session()->token(),
            'category_id' => $categoryId,
            'validation' => 1,
            'translations' => $this->minimalTranslationsPayload($name),
        ]);

        $tag = Tag::query()->where('tag_category_id', $categoryId)->latest('id')->first();
        $this->assertNotNull($tag);

        $response->assertRedirect(route('admin.taxonomy.tags.show', $tag));
        $response->assertSessionHas('success');
        $this->assertTrue($tag->validation);
        $tag->load('translations');
        $this->assertTrue(
            $tag->translations->contains(fn ($t): bool => $t->name === $name)
        );
    }

    public function test_store_redirects_with_errors_when_category_id_missing(): void
    {
        $this->actingAs($this->createAdmin());
        $this->from(route('admin.taxonomy.tags.create'));

        $this->post(route('admin.taxonomy.tags.store'), [
            '_token' => session()->token(),
            'translations' => $this->minimalTranslationsPayload('NameOnly'),
        ])
            ->assertRedirect(route('admin.taxonomy.tags.create'))
            ->assertSessionHasErrors('category_id');
    }

    public function test_store_redirects_with_errors_when_translations_invalid(): void
    {
        $this->actingAs($this->createAdmin());
        $this->from(route('admin.taxonomy.tags.create'));

        $this->post(route('admin.taxonomy.tags.store'), [
            '_token' => session()->token(),
            'category_id' => $this->tagCategoryId(),
            'validation' => 1,
            'translations' => [
                'universal' => [],
                'pt_BR' => ['name' => ''],
                'en' => [],
            ],
        ])
            ->assertRedirect(route('admin.taxonomy.tags.create'))
            ->assertSessionHasErrors('translations');
    }

    public function test_admin_can_view_show(): void
    {
        $tag = $this->createTagWithName('AdminTagShowView');

        $this->actingAs($this->createAdmin());

        $this->get(route('admin.taxonomy.tags.show', $tag))
            ->assertOk()
            ->assertViewIs('pages.admin.taxonomy.tags.show')
            ->assertViewHas('tag');
    }

    public function test_show_returns_not_found_for_unknown_tag_id(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.taxonomy.tags.show', ['tag' => 9_999_999]))
            ->assertNotFound();
    }

    public function test_admin_can_open_edit_form(): void
    {
        $tag = $this->createTagWithName('AdminTagEditForm');

        $this->actingAs($this->createAdmin());

        $this->get(route('admin.taxonomy.tags.edit', $tag))
            ->assertOk()
            ->assertViewIs('pages.admin.taxonomy.tags.edit')
            ->assertViewHas(['tag', 'categories', 'contentLanguages']);
    }

    public function test_admin_can_update_tag_and_redirect_to_show(): void
    {
        $tag = $this->createTagWithName('BeforeTagUpdate');

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.taxonomy.tags.edit', $tag));

        $updated = 'AfterTagUpdate_' . uniqid('', false);
        $response = $this->put(route('admin.taxonomy.tags.update', $tag), [
            '_token' => session()->token(),
            'category_id' => $tag->tag_category_id,
            'validation' => 1,
            'translations' => $this->minimalTranslationsPayload($updated),
        ]);

        $response->assertRedirect(route('admin.taxonomy.tags.show', $tag));
        $response->assertSessionHas('success');

        $tag->refresh();
        $tag->load('translations');
        $this->assertTrue(
            $tag->translations->contains(fn ($t): bool => $t->name === $updated)
        );
    }

    public function test_admin_can_destroy_tag_without_item_links(): void
    {
        $categoryId = $this->tagCategoryId();
        $name = 'AdminTagDestroy_' . uniqid('', false);

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.taxonomy.tags.create'));

        $this->post(route('admin.taxonomy.tags.store'), [
            '_token' => session()->token(),
            'category_id' => $categoryId,
            'validation' => 1,
            'translations' => $this->minimalTranslationsPayload($name),
        ]);

        $tag = Tag::query()->where('tag_category_id', $categoryId)->latest('id')->first();
        $this->assertNotNull($tag);

        $this->get(route('admin.taxonomy.tags.index'));

        $response = $this->delete(route('admin.taxonomy.tags.destroy', $tag), [
            '_token' => session()->token(),
        ]);

        $response->assertRedirect(route('admin.taxonomy.tags.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    private function createAdmin(): Admin
    {
        return Admin::create([
            'username' => 'admin_tag_ctrl_' . uniqid('', false),
            'password' => Hash::make('secret'),
        ]);
    }

    private function tagCategoryId(): int
    {
        $id = TagCategory::query()->orderBy('id')->value('id');
        $this->assertNotNull($id);

        return (int) $id;
    }

    private function createTagWithName(string $name): Tag
    {
        $created = TagFactory::new()->create([
            'tag_category_id' => $this->tagCategoryId(),
            'validation' => true,
        ]);
        $this->assertInstanceOf(Tag::class, $created);
        $created->syncPrimaryLocaleTranslation(['name' => $name]);

        return $created;
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
