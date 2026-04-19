<?php

namespace Tests\Feature\Catalog;

use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Models\Language;
use Database\Factories\Catalog\ItemCategoryFactory;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
class ItemControllerTest extends AbstractMysqlRefreshDatabaseTestCase
{
    /**
     * @param  array<string, string|int>  $query
     */
    private function catalogIndexUrl(array $query = []): string
    {
        $base = [
            'item_category' => '',
            'search' => '',
            'order' => '1',
        ];

        return route('catalog.items.index', [], false) . '?' . http_build_query(array_merge($base, $query));
    }

    public function test_index_redirects_to_default_query_when_query_string_is_empty(): void
    {
        $expected = route('catalog.items.index', [], false) . '?' . http_build_query([
            'item_category' => '',
            'search' => '',
            'order' => '1',
        ]);

        $this->get(route('catalog.items.index', [], false))
            ->assertRedirect($expected);
    }

    public function test_index_renders_catalog_index_view_with_default_filters(): void
    {
        $this->get($this->catalogIndexUrl())
            ->assertOk()
            ->assertViewIs('pages.catalog.items.index')
            ->assertViewHas([
                'items',
                'categoryName',
                'itemCategories',
                'categories',
                'locations',
            ]);
    }

    public function test_index_does_not_redirect_when_query_string_is_non_empty(): void
    {
        $url = route('catalog.items.index', [], false) . '?order=2';

        $this->get($url)
            ->assertOk()
            ->assertViewIs('pages.catalog.items.index');
    }

    public function test_index_search_finds_item_by_translated_name(): void
    {
        $category = ItemCategoryFactory::new()->create();
        $collaborator = Collaborator::factory()->create();
        $item = Item::factory()->create([
            'category_id' => $category->id,
            'collaborator_id' => $collaborator->id,
            'validation' => true,
        ]);
        $needle = 'ItemCtrlIndexSearch_' . uniqid('', false);
        $item->syncPrimaryLocaleTranslation([
            'name' => $needle,
            'description' => 'd',
            'history' => 'h',
            'detail' => 't',
        ]);

        $this->get($this->catalogIndexUrl(['search' => $needle]))
            ->assertOk()
            ->assertSee($needle, false);
    }

    public function test_create_renders_contribution_form(): void
    {
        $this->get(route('catalog.items.create'))
            ->assertOk()
            ->assertViewIs('pages.catalog.items.create')
            ->assertViewHas([
                'categories',
                'itemCategories',
                'contributionLanguages',
                'defaultContributionContentLocale',
                'locations',
                'defaultCatalogLocationId',
            ]);
    }

    public function test_show_renders_public_item_when_validated(): void
    {
        $category = ItemCategoryFactory::new()->create();
        $collaborator = Collaborator::factory()->create();
        $item = Item::factory()->create([
            'category_id' => $category->id,
            'collaborator_id' => $collaborator->id,
            'validation' => true,
        ]);

        $this->get(route('catalog.items.show', ['id' => $item->id]))
            ->assertOk()
            ->assertViewIs('pages.catalog.items.show')
            ->assertViewHas([
                'item',
                'itemCategories',
                'categories',
                'seriesCategoryId',
                'contributionLanguages',
                'defaultExtraContentLocale',
            ]);
    }

    public function test_show_route_returns_not_found_for_non_numeric_id(): void
    {
        $this->get('/catalog/items/not-a-number')->assertNotFound();
    }

    public function test_show_returns_403_when_item_not_validated(): void
    {
        $category = ItemCategoryFactory::new()->create();
        $collaborator = Collaborator::factory()->create();
        $item = Item::factory()->create([
            'category_id' => $category->id,
            'collaborator_id' => $collaborator->id,
            'validation' => false,
        ]);

        $this->get(route('catalog.items.show', ['id' => $item->id]))
            ->assertForbidden();
    }

    public function test_show_returns_404_for_unknown_item_id(): void
    {
        $this->get(route('catalog.items.show', ['id' => 9_999_999]))
            ->assertNotFound();
    }

    public function test_by_category_returns_empty_json_for_invalid_category(): void
    {
        $this->getJson(route('catalog.items.byCategory', ['item_category' => '']))
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_by_category_returns_json_for_items_in_category(): void
    {
        $category = ItemCategoryFactory::new()->create();
        $collaborator = Collaborator::factory()->create();
        $item = Item::factory()->create([
            'category_id' => $category->id,
            'collaborator_id' => $collaborator->id,
            'validation' => true,
        ]);

        $this->getJson(route('catalog.items.byCategory', ['item_category' => (string) $category->id]))
            ->assertOk()
            ->assertJsonFragment(['id' => $item->id]);
    }

    public function test_by_category_returns_422_for_unknown_content_locale(): void
    {
        $this->getJson(route('catalog.items.byCategory', [
            'item_category' => '1',
            'content_locale' => '__invalid_locale__',
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['content_locale']);
    }

    public function test_by_category_with_valid_content_locale_returns_ok(): void
    {
        $enCode = Language::query()->where('code', 'en')->value('code');
        $this->assertNotNull($enCode, 'Expected languages migration to seed the `en` language row.');

        $category = ItemCategoryFactory::new()->create();
        $collaborator = Collaborator::factory()->create();
        $item = Item::factory()->create([
            'category_id' => $category->id,
            'collaborator_id' => $collaborator->id,
            'validation' => true,
        ]);

        $this->getJson(route('catalog.items.byCategory', [
            'item_category' => (string) $category->id,
            'content_locale' => (string) $enCode,
        ]))
            ->assertOk()
            ->assertJsonFragment(['id' => $item->id]);
    }

    public function test_by_category_json_excludes_unvalidated_items(): void
    {
        $category = ItemCategoryFactory::new()->create();
        $collaborator = Collaborator::factory()->create();
        $validated = Item::factory()->create([
            'category_id' => $category->id,
            'collaborator_id' => $collaborator->id,
            'validation' => true,
            'identification_code' => 'BY_CAT_OK',
        ]);
        $draft = Item::factory()->create([
            'category_id' => $category->id,
            'collaborator_id' => $collaborator->id,
            'validation' => false,
            'identification_code' => 'BY_CAT_DRAFT',
        ]);

        $payload = $this->getJson(route('catalog.items.byCategory', ['item_category' => (string) $category->id]))
            ->assertOk()
            ->json();

        $ids = array_column($payload, 'id');
        $this->assertContains($validated->id, $ids);
        $this->assertNotContains($draft->id, $ids);
    }

    public function test_component_autocomplete_returns_empty_json_for_invalid_category(): void
    {
        $this->getJson(route('catalog.items.component-autocomplete', [
            'query' => 'x',
            'category' => '',
        ]))
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_component_autocomplete_returns_matching_validated_items(): void
    {
        $category = ItemCategoryFactory::new()->create();
        $collaborator = Collaborator::factory()->create();
        $item = Item::factory()->create([
            'category_id' => $category->id,
            'collaborator_id' => $collaborator->id,
            'validation' => true,
        ]);
        $item->syncPrimaryLocaleTranslation([
            'name' => 'UniqueAutocompleteMarkerXYZ',
            'description' => 'd',
            'history' => 'h',
            'detail' => 't',
        ]);

        $this->getJson(route('catalog.items.component-autocomplete', [
            'query' => 'UniqueAutocompleteMarker',
            'category' => (string) $category->id,
        ]))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $item->id,
                'name' => 'UniqueAutocompleteMarkerXYZ',
            ]);
    }

    public function test_component_autocomplete_with_empty_query_returns_items_in_category(): void
    {
        $category = ItemCategoryFactory::new()->create();
        $collaborator = Collaborator::factory()->create();
        $item = Item::factory()->create([
            'category_id' => $category->id,
            'collaborator_id' => $collaborator->id,
            'validation' => true,
        ]);
        $item->syncPrimaryLocaleTranslation([
            'name' => 'EmptyQueryRowName',
            'description' => 'd',
            'history' => 'h',
            'detail' => 't',
        ]);

        $this->getJson(route('catalog.items.component-autocomplete', [
            'query' => '',
            'category' => (string) $category->id,
        ]))
            ->assertOk()
            ->assertJsonFragment(['id' => $item->id]);
    }

    public function test_component_autocomplete_excludes_unvalidated_items(): void
    {
        $category = ItemCategoryFactory::new()->create();
        $collaborator = Collaborator::factory()->create();
        $validated = Item::factory()->create([
            'category_id' => $category->id,
            'collaborator_id' => $collaborator->id,
            'validation' => true,
        ]);
        $validated->syncPrimaryLocaleTranslation([
            'name' => 'SharedMarkerOnlyValidated',
            'description' => 'd',
            'history' => 'h',
            'detail' => 't',
        ]);
        $draft = Item::factory()->create([
            'category_id' => $category->id,
            'collaborator_id' => $collaborator->id,
            'validation' => false,
        ]);
        $draft->syncPrimaryLocaleTranslation([
            'name' => 'SharedMarkerOnlyValidated',
            'description' => 'd',
            'history' => 'h',
            'detail' => 't',
        ]);

        $rows = $this->getJson(route('catalog.items.component-autocomplete', [
            'query' => 'SharedMarkerOnly',
            'category' => (string) $category->id,
        ]))
            ->assertOk()
            ->json();

        $ids = array_column($rows, 'id');
        $this->assertContains($validated->id, $ids);
        $this->assertNotContains($draft->id, $ids);
    }

    public function test_check_component_name_returns_json_count(): void
    {
        $category = ItemCategoryFactory::new()->create();
        $collaborator = Collaborator::factory()->create();
        $item = Item::factory()->create([
            'category_id' => $category->id,
            'collaborator_id' => $collaborator->id,
            'validation' => true,
        ]);
        $exactName = 'ExactNameForCountProbe_' . uniqid('', false);
        $item->syncPrimaryLocaleTranslation([
            'name' => $exactName,
            'description' => 'd',
            'history' => 'h',
            'detail' => 't',
        ]);

        $langId = Language::idForPreferredFormLocale();
        $item->translations()->updateOrCreate(
            ['language_id' => $langId],
            [
                'name' => $exactName,
                'description' => 'd',
                'history' => 'h',
                'detail' => 't',
            ]
        );

        $sameName = $this->getJson(route('catalog.items.check-component-name', [
            'category' => (string) $category->id,
            'name' => $exactName,
        ]));
        $sameName->assertOk();
        $sameBody = $sameName->getContent();
        $this->assertIsString($sameBody);
        $this->assertSame(1, json_decode($sameBody, true));

        $missing = $this->getJson(route('catalog.items.check-component-name', [
            'category' => (string) $category->id,
            'name' => $exactName . '_missing',
        ]));
        $missing->assertOk();
        $missingBody = $missing->getContent();
        $this->assertIsString($missingBody);
        $this->assertSame(0, json_decode($missingBody, true));
    }

    public function test_check_component_name_returns_422_for_unknown_content_locale(): void
    {
        $this->getJson(route('catalog.items.check-component-name', [
            'category' => '1',
            'name' => 'x',
            'content_locale' => '__bad__',
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['content_locale']);
    }

    public function test_store_redirects_back_with_validation_errors_when_payload_invalid(): void
    {
        $this->get(route('catalog.items.create'));

        $this->from(route('catalog.items.create'))->post(route('catalog.items.store'), [
            '_token' => session()->token(),
        ])
            ->assertRedirect(route('catalog.items.create'))
            ->assertSessionHasErrors();
    }
}
