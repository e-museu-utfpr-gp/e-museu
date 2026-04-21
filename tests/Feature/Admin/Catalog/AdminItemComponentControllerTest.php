<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Catalog;

use App\Models\Catalog\Item;
use App\Models\Catalog\ItemComponent;
use App\Models\Collaborator\Collaborator;
use App\Models\Identity\Admin;
use App\Models\Location;
use Database\Factories\Catalog\ItemCategoryFactory;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
class AdminItemComponentControllerTest extends AbstractMysqlRefreshDatabaseTestCase
{
    public function test_guest_is_redirected_from_item_components_index_to_login(): void
    {
        $this->get(route('admin.catalog.item-components.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_item_components_create_to_login(): void
    {
        $this->get(route('admin.catalog.item-components.create'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_item_components_show_to_login(): void
    {
        $row = $this->createItemComponentRow();

        $this->get(route('admin.catalog.item-components.show', $row))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_store_item_component(): void
    {
        $this->get(route('login'));

        $this->post(route('admin.catalog.item-components.store'), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_update_item_component(): void
    {
        $row = $this->createItemComponentRow();
        $this->get(route('login'));

        $this->put(route('admin.catalog.item-components.update', $row), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_destroy_item_component(): void
    {
        $row = $this->createItemComponentRow();
        $this->get(route('login'));

        $this->delete(route('admin.catalog.item-components.destroy', $row), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_admin_can_view_item_components_index(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.catalog.item-components.index'))
            ->assertOk()
            ->assertViewIs('pages.admin.catalog.item-components.index')
            ->assertViewHas([
                'itemComponents',
                'count',
                'searchOptions',
                'sortColumns',
                'searchBooleanColumns',
            ]);
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.catalog.item-components.create'))
            ->assertOk()
            ->assertViewIs('pages.admin.catalog.item-components.create')
            ->assertViewHas('itemCategories');
    }

    public function test_admin_can_store_item_component_and_redirect_to_show(): void
    {
        $parent = $this->createItemWithFixtures();
        $child = $this->createItemWithFixtures();

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.catalog.item-components.create'));

        $response = $this->post(route('admin.catalog.item-components.store'), [
            '_token' => session()->token(),
            'item_id' => $parent->id,
            'component_id' => $child->id,
        ]);

        $row = ItemComponent::query()->latest('id')->first();
        $this->assertNotNull($row);
        $this->assertSame($parent->id, $row->item_id);
        $this->assertSame($child->id, $row->component_id);

        $response->assertRedirect(route('admin.catalog.item-components.show', $row));
        $response->assertSessionHas('success');
    }

    public function test_store_redirects_with_errors_when_component_same_as_parent_item(): void
    {
        $item = $this->createItemWithFixtures();

        $this->actingAs($this->createAdmin());
        $this->from(route('admin.catalog.item-components.create'));

        $this->post(route('admin.catalog.item-components.store'), [
            '_token' => session()->token(),
            'item_id' => $item->id,
            'component_id' => $item->id,
        ])
            ->assertRedirect(route('admin.catalog.item-components.create'))
            ->assertSessionHasErrors('component_id');
    }

    public function test_admin_can_view_show(): void
    {
        $row = $this->createItemComponentRow();

        $this->actingAs($this->createAdmin());

        $this->get(route('admin.catalog.item-components.show', $row))
            ->assertOk()
            ->assertViewIs('pages.admin.catalog.item-components.show')
            ->assertViewHas('itemComponent');
    }

    public function test_show_returns_not_found_for_unknown_item_component_id(): void
    {
        $this->actingAs($this->createAdmin());

        $this->get(route('admin.catalog.item-components.show', ['item_component' => 9_999_999]))
            ->assertNotFound();
    }

    public function test_admin_can_toggle_validation_via_update(): void
    {
        $row = $this->createItemComponentRow();
        $this->assertFalse($row->validation);

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.catalog.item-components.index'));

        $response = $this->put(route('admin.catalog.item-components.update', $row), [
            '_token' => session()->token(),
        ]);

        $response->assertRedirect(route('admin.catalog.item-components.show', $row));
        $response->assertSessionHas('success');

        $row->refresh();
        $this->assertTrue($row->validation);
    }

    public function test_admin_can_destroy_item_component(): void
    {
        $row = $this->createItemComponentRow();

        $this->actingAs($this->createAdmin());
        $this->get(route('admin.catalog.item-components.index'));

        $response = $this->delete(route('admin.catalog.item-components.destroy', $row), [
            '_token' => session()->token(),
        ]);

        $response->assertRedirect(route('admin.catalog.item-components.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('item_component', ['id' => $row->id]);
    }

    private function createAdmin(): Admin
    {
        return Admin::create([
            'username' => 'item_comp_ctrl_' . uniqid('', false),
            'password' => Hash::make('secret'),
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
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

    private function createItemComponentRow(): ItemComponent
    {
        $parent = $this->createItemWithFixtures();
        $child = $this->createItemWithFixtures();

        return ItemComponent::create([
            'item_id' => $parent->id,
            'component_id' => $child->id,
            'validation' => false,
        ]);
    }
}
