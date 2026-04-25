<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Catalog\Item;

use App\Models\Catalog\Item;
use App\Models\Catalog\ItemImage;
use App\Models\Collaborator\Collaborator;
use App\Models\Identity\Admin;
use App\Models\Location;
use Database\Factories\Catalog\ItemCategoryFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;
use Tests\Support\MinimalContributionCoverJpeg;

#[Group('mysql')]
class AdminItemControllerTest extends AbstractMysqlRefreshDatabaseTestCase
{
    public function test_guest_is_redirected_from_items_index_to_login(): void
    {
        $this->get(route('admin.catalog.items.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_items_create_to_login(): void
    {
        $this->get(route('admin.catalog.items.create'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_items_show_to_login(): void
    {
        $item = $this->createItemWithFixtures();

        $this->get(route('admin.catalog.items.show', $item))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_items_edit_to_login(): void
    {
        $item = $this->createItemWithFixtures();

        $this->get(route('admin.catalog.items.edit', $item))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_items_by_item_category_to_login(): void
    {
        $url = route('admin.catalog.items.by-item-category', ['item_category' => '1']);

        $this->get($url)->assertRedirect(route('login'));
    }

    public function test_guest_cannot_store_item(): void
    {
        $this->get(route('login'));

        $this->post(route('admin.catalog.items.store'), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_update_item(): void
    {
        $item = $this->createItemWithFixtures();
        $this->get(route('login'));

        $this->put(route('admin.catalog.items.update', $item), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_destroy_item(): void
    {
        $item = $this->createItemWithFixtures();
        $this->get(route('login'));

        $this->delete(route('admin.catalog.items.destroy', $item), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_regenerate_item_qr_code(): void
    {
        $item = $this->createItemWithFixtures();
        $this->get(route('login'));

        $this->post(route('admin.catalog.items.qrcode.regenerate', $item), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_delete_item_qr_code(): void
    {
        $item = $this->createItemWithFixtures();
        $this->get(route('login'));

        $this->delete(route('admin.catalog.items.qrcode.delete', $item), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_guest_cannot_destroy_item_image(): void
    {
        Storage::fake('public');
        $item = $this->createItemWithFixtures();
        $path = 'items/' . $item->id . '/guest-block.png';
        Storage::disk('public')->put($path, 'x');
        $image = ItemImage::query()->create([
            'item_id' => $item->id,
            'path' => $path,
            'type' => 'gallery',
            'sort_order' => 1,
        ]);
        $this->get(route('login'));

        $this->delete(route('admin.catalog.items.images.destroy', [$item, $image]), [
            '_token' => session()->token(),
        ])->assertRedirect(route('login'));
    }

    public function test_admin_can_view_items_index(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $this->get(route('admin.catalog.items.index'))
            ->assertOk()
            ->assertViewIs('pages.admin.catalog.items.index');
    }

    public function test_admin_can_view_items_create_form(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $this->get(route('admin.catalog.items.create'))
            ->assertOk()
            ->assertViewIs('pages.admin.catalog.items.create');
    }

    public function test_admin_can_store_item_and_redirect_to_show(): void
    {
        Storage::fake('public');

        $admin = $this->createAdmin();
        $category = ItemCategoryFactory::new()->create();
        $location = Location::factory()->create();
        $collaborator = Collaborator::factory()->create();

        $this->actingAs($admin);
        $this->get(route('admin.catalog.items.create'));

        $cover = UploadedFile::fake()->createWithContent(
            'cover.jpg',
            MinimalContributionCoverJpeg::binary()
        );

        $response = $this->post(route('admin.catalog.items.store'), [
            '_token' => session()->token(),
            'date' => '2019-03-10',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'collaborator_id' => $collaborator->id,
            'validation' => 1,
            'cover_image' => $cover,
            'translations' => $this->minimalTranslationsPayload(),
        ]);

        $item = Item::query()->where('category_id', $category->id)->latest('id')->first();
        $this->assertNotNull($item);

        $response->assertRedirect(route('admin.catalog.items.show', $item));
        $response->assertSessionHas('success');

        $this->assertSame('2019-03-10', $item->date?->format('Y-m-d'));
        $this->assertTrue($item->validation);
        $this->assertNotSame('000', $item->identification_code);
    }

    public function test_admin_can_view_item_show(): void
    {
        $admin = $this->createAdmin();
        $item = $this->createItemWithFixtures();

        $this->actingAs($admin);

        $this->get(route('admin.catalog.items.show', $item))
            ->assertOk()
            ->assertViewIs('pages.admin.catalog.items.show')
            ->assertViewHas('item');
    }

    public function test_admin_can_view_item_edit(): void
    {
        $admin = $this->createAdmin();
        $item = $this->createItemWithFixtures();

        $this->actingAs($admin);

        $this->get(route('admin.catalog.items.edit', $item))
            ->assertOk()
            ->assertViewIs('pages.admin.catalog.items.edit')
            ->assertViewHas('item');
    }

    public function test_admin_can_update_item(): void
    {
        $admin = $this->createAdmin();
        $item = $this->createItemWithFixtures();

        $this->actingAs($admin);
        $this->get(route('admin.catalog.items.edit', $item));

        $response = $this->put(route('admin.catalog.items.update', $item), [
            '_token' => session()->token(),
            'date' => '2021-07-01',
            'category_id' => $item->category_id,
            'location_id' => $item->location_id,
            'collaborator_id' => $item->collaborator_id,
            'identification_code' => $item->identification_code,
            'validation' => 1,
            'translations' => [
                'universal' => [],
                'pt_BR' => [
                    'name' => 'Updated title',
                    'description' => 'Updated description',
                    'detail' => '',
                    'history' => '',
                ],
                'en' => [],
            ],
        ]);

        $response->assertRedirect(route('admin.catalog.items.show', $item));
        $response->assertSessionHas('success');

        $item->refresh();
        $this->assertSame('2021-07-01', $item->date?->format('Y-m-d'));
        $item->load('translations');
        $pt = $item->translations->firstWhere('language_id', 2);
        $this->assertNotNull($pt);
        $this->assertSame('Updated title', $pt->name);
    }

    public function test_admin_update_regenerates_qr_when_identification_code_changes(): void
    {
        Storage::fake('public');
        Http::fake([
            'api.qrserver.com/*' => Http::response("\x89PNG\r\n\x1a\n", 200, ['Content-Type' => 'image/png']),
        ]);

        $admin = $this->createAdmin();
        $item = $this->createItemWithFixtures([
            'identification_code' => 'QR_KEEP_' . uniqid('', false),
        ]);
        $previousCode = (string) $item->identification_code;
        $newCode = 'QR_NEW_' . uniqid('', false);
        $this->assertNotSame($previousCode, $newCode);

        $this->actingAs($admin);
        $this->get(route('admin.catalog.items.edit', $item));

        $response = $this->put(route('admin.catalog.items.update', $item), [
            '_token' => session()->token(),
            'date' => $item->date?->format('Y-m-d'),
            'category_id' => $item->category_id,
            'location_id' => $item->location_id,
            'collaborator_id' => $item->collaborator_id,
            'identification_code' => $newCode,
            'validation' => 1,
            'translations' => $this->minimalTranslationsPayload(),
        ]);

        $response->assertRedirect(route('admin.catalog.items.show', $item));
        $item->refresh();
        $this->assertSame($newCode, $item->identification_code);
        Http::assertSentCount(1);
    }

    public function test_admin_can_destroy_item(): void
    {
        Storage::fake('public');

        $admin = $this->createAdmin();
        $item = $this->createItemWithFixtures();

        $this->actingAs($admin);
        $this->get(route('admin.catalog.items.index'));

        $response = $this->delete(route('admin.catalog.items.destroy', $item), [
            '_token' => session()->token(),
        ]);

        $response->assertRedirect(route('admin.catalog.items.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }

    public function test_by_item_category_returns_json_for_category(): void
    {
        $admin = $this->createAdmin();
        $catA = ItemCategoryFactory::new()->create();
        $catB = ItemCategoryFactory::new()->create();
        $itemA = $this->createItemWithFixtures(['category_id' => $catA->id]);
        $this->createItemWithFixtures(['category_id' => $catB->id]);

        $this->actingAs($admin);

        $url = route('admin.catalog.items.by-item-category', ['item_category' => $catA->id]);

        $this->get($url)
            ->assertOk()
            ->assertJsonFragment(['id' => $itemA->id]);
    }

    public function test_by_item_category_returns_empty_json_when_category_invalid(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $url = route('admin.catalog.items.by-item-category', ['item_category' => '']);

        $this->get($url)->assertOk()->assertExactJson([]);
    }

    public function test_admin_can_regenerate_qr_code(): void
    {
        Storage::fake('public');
        Http::fake([
            'api.qrserver.com/*' => Http::response("\x89PNG\r\n\x1a\n", 200, ['Content-Type' => 'image/png']),
        ]);

        $admin = $this->createAdmin();
        $item = $this->createItemWithFixtures();

        $this->actingAs($admin);
        $this->get(route('admin.catalog.items.edit', $item));

        $response = $this->post(
            route('admin.catalog.items.qrcode.regenerate', $item),
            ['_token' => session()->token()]
        );

        $response->assertRedirect(route('admin.catalog.items.edit', $item));
        $response->assertSessionHas('success');
        Http::assertSentCount(1);
    }

    public function test_admin_qr_regenerate_redirects_with_error_when_external_http_fails(): void
    {
        Storage::fake('public');
        Http::fake([
            'api.qrserver.com/*' => Http::response('', 503),
        ]);

        $admin = $this->createAdmin();
        $item = $this->createItemWithFixtures();

        $this->actingAs($admin);
        $this->get(route('admin.catalog.items.edit', $item));

        $response = $this->post(
            route('admin.catalog.items.qrcode.regenerate', $item),
            ['_token' => session()->token()]
        );

        $response->assertRedirect(route('admin.catalog.items.edit', $item));
        $response->assertSessionHasErrors('qrcode');
        $response->assertSessionMissing('success');
    }

    public function test_admin_can_delete_qr_code(): void
    {
        Storage::fake('public');
        Http::fake([
            'api.qrserver.com/*' => Http::response("\x89PNG\r\n\x1a\n", 200, ['Content-Type' => 'image/png']),
        ]);

        $admin = $this->createAdmin();
        $item = $this->createItemWithFixtures();

        $this->actingAs($admin);
        $this->get(route('admin.catalog.items.edit', $item));
        $this->post(
            route('admin.catalog.items.qrcode.regenerate', $item),
            ['_token' => session()->token()]
        );

        $this->get(route('admin.catalog.items.edit', $item));

        $response = $this->delete(
            route('admin.catalog.items.qrcode.delete', $item),
            ['_token' => session()->token()]
        );

        $response->assertRedirect(route('admin.catalog.items.edit', $item));
        $response->assertSessionHas('success');
        $this->assertSame(0, $item->images()->where('type', 'qrcode')->count());
    }

    public function test_admin_can_destroy_item_image(): void
    {
        Storage::fake('public');

        $admin = $this->createAdmin();
        $item = $this->createItemWithFixtures();
        $path = 'items/' . $item->id . '/gallery-test.png';
        Storage::disk('public')->put($path, 'x');

        $image = ItemImage::query()->create([
            'item_id' => $item->id,
            'path' => $path,
            'type' => 'gallery',
            'sort_order' => 5,
        ]);

        $this->actingAs($admin);
        $this->get(route('admin.catalog.items.edit', $item));

        $response = $this->delete(
            route('admin.catalog.items.images.destroy', [$item, $image]),
            ['_token' => session()->token()]
        );

        $response->assertRedirect(route('admin.catalog.items.edit', $item));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('item_images', ['id' => $image->id]);
    }

    private function createAdmin(): Admin
    {
        return Admin::create([
            'username' => 'item_ctrl_' . uniqid('', false),
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
     * @return array<string, array<string, string>>
     */
    private function minimalTranslationsPayload(): array
    {
        return [
            'universal' => [],
            'pt_BR' => [
                'name' => 'Admin test item',
                'description' => 'Minimal description.',
                'detail' => '',
                'history' => '',
            ],
            'en' => [],
        ];
    }
}
