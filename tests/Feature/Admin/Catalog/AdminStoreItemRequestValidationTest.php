<?php

namespace Tests\Feature\Admin\Catalog;

use App\Models\Catalog\ItemCategory;
use App\Models\Collaborator\Collaborator;
use App\Models\Identity\Admin;
use App\Models\Location;
use Illuminate\Http\Testing\File as HttpTestingFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;
use Tests\Support\MinimalContributionCoverJpeg;

/**
 * HTTP-level validation for {@see \App\Http\Requests\Admin\Catalog\AdminStoreItemRequest}
 * (beyond happy-path {@see AdminItemControllerTest}).
 */
#[Group('mysql')]
final class AdminStoreItemRequestValidationTest extends AbstractMysqlRefreshDatabaseTestCase
{
    private function actingAdmin(): Admin
    {
        return Admin::create([
            'username' => 'store_val_' . uniqid('', false),
            'password' => Hash::make('secret'),
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
                'name' => 'Title',
                'description' => 'Description.',
                'detail' => '',
                'history' => '',
            ],
            'en' => [],
        ];
    }

    /**
     * @return array{
     *     category: ItemCategory,
     *     location: Location,
     *     collaborator: Collaborator,
     *     cover: HttpTestingFile
     * }
     */
    private function beginAuthenticatedItemStoreForm(): array
    {
        $admin = $this->actingAdmin();
        $category = ItemCategory::factory()->create();
        $location = Location::factory()->create();
        $collaborator = Collaborator::factory()->create();
        $cover = UploadedFile::fake()->createWithContent(
            'cover.jpg',
            MinimalContributionCoverJpeg::binary()
        );

        $this->actingAs($admin);
        $this->get(route('admin.catalog.items.create'));

        return [
            'category' => $category,
            'location' => $location,
            'collaborator' => $collaborator,
            'cover' => $cover,
        ];
    }

    public function test_store_rejects_when_cover_image_missing(): void
    {
        $admin = $this->actingAdmin();
        $category = ItemCategory::factory()->create();
        $location = Location::factory()->create();
        $collaborator = Collaborator::factory()->create();

        $this->actingAs($admin);
        $this->get(route('admin.catalog.items.create'));

        $response = $this->post(route('admin.catalog.items.store'), [
            '_token' => session()->token(),
            'date' => '2019-03-10',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'collaborator_id' => $collaborator->id,
            'validation' => 1,
            'translations' => $this->minimalTranslationsPayload(),
        ]);

        $response->assertSessionHasErrors('cover_image');
    }

    public function test_store_rejects_when_no_complete_translation_locale(): void
    {
        $ctx = $this->beginAuthenticatedItemStoreForm();

        $response = $this->post(route('admin.catalog.items.store'), [
            '_token' => session()->token(),
            'date' => '2019-03-10',
            'category_id' => $ctx['category']->id,
            'location_id' => $ctx['location']->id,
            'collaborator_id' => $ctx['collaborator']->id,
            'validation' => 1,
            'cover_image' => $ctx['cover'],
            'translations' => [
                'universal' => [],
                'pt_BR' => ['name' => '', 'description' => '', 'detail' => '', 'history' => ''],
                'en' => [],
            ],
        ]);

        $response->assertSessionHasErrors('translations');
    }

    public function test_store_rejects_when_translation_name_exceeds_max_length(): void
    {
        $ctx = $this->beginAuthenticatedItemStoreForm();

        $response = $this->post(route('admin.catalog.items.store'), [
            '_token' => session()->token(),
            'date' => '2019-03-10',
            'category_id' => $ctx['category']->id,
            'location_id' => $ctx['location']->id,
            'collaborator_id' => $ctx['collaborator']->id,
            'validation' => 1,
            'cover_image' => $ctx['cover'],
            'translations' => [
                'universal' => [],
                'pt_BR' => [
                    'name' => str_repeat('x', 201),
                    'description' => 'Ok description here.',
                    'detail' => '',
                    'history' => '',
                ],
                'en' => [],
            ],
        ]);

        $response->assertSessionHasErrors('translations.pt_BR.name');
    }
}
