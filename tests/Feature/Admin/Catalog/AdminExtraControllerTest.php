<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Catalog;

use App\Models\Catalog\Extra;
use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Models\Identity\Admin;
use App\Models\Location;
use Database\Factories\Catalog\ItemCategoryFactory;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
class AdminExtraControllerTest extends AbstractMysqlRefreshDatabaseTestCase
{
    public function test_guest_is_redirected_from_extras_index_to_login(): void
    {
        $this->get(route('admin.catalog.extras.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_extras_create_to_login(): void
    {
        $this->get(route('admin.catalog.extras.create'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_extras_show_to_login(): void
    {
        $extra = $this->createExtraWithFixtures();

        $this->get(route('admin.catalog.extras.show', $extra))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_extras_edit_to_login(): void
    {
        $extra = $this->createExtraWithFixtures();

        $this->get(route('admin.catalog.extras.edit', $extra))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_store_extra(): void
    {
        $this->get(route('login'));

        $this->post(route('admin.catalog.extras.store'), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_update_extra(): void
    {
        $extra = $this->createExtraWithFixtures();
        $this->get(route('login'));

        $this->put(route('admin.catalog.extras.update', $extra), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_destroy_extra(): void
    {
        $extra = $this->createExtraWithFixtures();
        $this->get(route('login'));

        $this->delete(route('admin.catalog.extras.destroy', $extra), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_admin_can_view_extras_index(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.catalog.extras.index'))
            ->assertOk()
            ->assertViewIs('pages.admin.catalog.extras.index');
    }

    public function test_admin_can_view_extras_create_form(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.catalog.extras.create'))
            ->assertOk()
            ->assertViewIs('pages.admin.catalog.extras.create');
    }

    public function test_admin_can_store_extra_and_redirect_to_show(): void
    {
        $item = $this->createItemWithFixtures();
        $collaborator = Collaborator::factory()->create();

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.catalog.extras.create'));

        $response = $this->post(route('admin.catalog.extras.store'), [
            '_token' => session()->token(),
            'item_id' => $item->id,
            'collaborator_id' => $collaborator->id,
            'validation' => 1,
            'translations' => $this->minimalTranslationsPayload(),
        ]);

        $extra = Extra::query()->where('item_id', $item->id)->latest('id')->first();
        $this->assertNotNull($extra);

        $response->assertRedirect(route('admin.catalog.extras.show', $extra));
        $response->assertSessionHas('success');

        $this->assertTrue($extra->validation);
        $extra->load('translations');
        $pt = $extra->translations->firstWhere('language_id', 2);
        $this->assertNotNull($pt);
        $this->assertSame('Supplementary info (pt_BR)', $pt->info);
    }

    public function test_store_rejects_when_no_locale_has_info(): void
    {
        $item = $this->createItemWithFixtures();
        $collaborator = Collaborator::factory()->create();

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.catalog.extras.create'));

        $response = $this->post(route('admin.catalog.extras.store'), [
            '_token' => session()->token(),
            'item_id' => $item->id,
            'collaborator_id' => $collaborator->id,
            'validation' => 1,
            'translations' => [
                'universal' => [],
                'pt_BR' => ['info' => ''],
                'en' => [],
            ],
        ]);

        $response->assertSessionHasErrors('translations');
        $this->assertSame(0, Extra::query()->where('item_id', $item->id)->count());
    }

    public function test_admin_can_view_extra_show(): void
    {
        $extra = $this->createExtraWithFixtures();

        $this->actingAs($this->createAdmin());

        $this->get(route('admin.catalog.extras.show', $extra))
            ->assertOk()
            ->assertViewIs('pages.admin.catalog.extras.show')
            ->assertViewHas('extra');
    }

    public function test_admin_can_view_extra_edit(): void
    {
        $extra = $this->createExtraWithFixtures();

        $this->actingAs($this->createAdmin());

        $this->get(route('admin.catalog.extras.edit', $extra))
            ->assertOk()
            ->assertViewIs('pages.admin.catalog.extras.edit')
            ->assertViewHas('extra');
    }

    public function test_admin_can_update_extra(): void
    {
        $extra = $this->createExtraWithFixtures();

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.catalog.extras.edit', $extra));

        $response = $this->put(route('admin.catalog.extras.update', $extra), [
            '_token' => session()->token(),
            'item_id' => $extra->item_id,
            'collaborator_id' => $extra->collaborator_id,
            'validation' => 1,
            'translations' => [
                'universal' => [],
                'pt_BR' => [
                    'info' => 'Updated text',
                ],
                'en' => [],
            ],
        ]);

        $response->assertRedirect(route('admin.catalog.extras.show', $extra));
        $response->assertSessionHas('success');

        $extra->refresh();
        $extra->load('translations');
        $pt = $extra->translations->firstWhere('language_id', 2);
        $this->assertNotNull($pt);
        $this->assertSame('Updated text', $pt->info);
    }

    public function test_admin_can_destroy_extra(): void
    {
        $extra = $this->createExtraWithFixtures();

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.catalog.extras.index'));

        $response = $this->delete(route('admin.catalog.extras.destroy', $extra), [
            '_token' => session()->token(),
        ]);

        $response->assertRedirect(route('admin.catalog.extras.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('extras', ['id' => $extra->id]);
    }

    private function createAdmin(): Admin
    {
        return Admin::create([
            'username' => 'extra_ctrl_' . uniqid('', false),
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

    private function createExtraWithFixtures(): Extra
    {
        $item = $this->createItemWithFixtures();
        $collaborator = Collaborator::factory()->create();

        return Extra::factory()->create([
            'item_id' => $item->id,
            'collaborator_id' => $collaborator->id,
        ]);
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function minimalTranslationsPayload(): array
    {
        return [
            'universal' => [],
            'pt_BR' => [
                'info' => 'Supplementary info (pt_BR)',
            ],
            'en' => [],
        ];
    }
}
