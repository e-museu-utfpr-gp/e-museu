<?php

namespace Tests\Feature\Catalog;

use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Models\Location;
use Database\Factories\Catalog\ItemCategoryFactory;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
class ItemCatalogIndexLocationFilterTest extends AbstractMysqlRefreshDatabaseTestCase
{
    /**
     * @param  array<string, string|int>  $query
     */
    private function catalogIndexUrl(array $query): string
    {
        return route('catalog.items.index', [], false) . '?' . http_build_query(array_merge([
            'item_category' => '',
            'search' => '',
            'order' => '1',
            'location_id' => '',
        ], $query));
    }

    public function test_catalog_index_filters_validated_items_by_location_id(): void
    {
        $category = ItemCategoryFactory::new()->create();
        $collaborator = Collaborator::factory()->create();
        $utfpr = Location::query()->where('code', 'UTFPR')->firstOrFail();
        $uncen = Location::query()->where('code', 'UNCEN')->firstOrFail();

        Item::factory()->create([
            'category_id' => $category->id,
            'collaborator_id' => $collaborator->id,
            'location_id' => $utfpr->id,
            'validation' => true,
            'identification_code' => 'LOC_IX_UTFPR_ONLY',
        ]);
        Item::factory()->create([
            'category_id' => $category->id,
            'collaborator_id' => $collaborator->id,
            'location_id' => $uncen->id,
            'validation' => true,
            'identification_code' => 'LOC_IX_UNCEN_ONLY',
        ]);

        $response = $this->get($this->catalogIndexUrl([
            'location_id' => (string) $utfpr->id,
        ]));

        $response->assertOk();
        $response->assertSee('LOC_IX_UTFPR_ONLY', false);
        $response->assertDontSee('LOC_IX_UNCEN_ONLY', false);
    }

    public function test_catalog_index_non_numeric_location_id_yields_no_items(): void
    {
        $category = ItemCategoryFactory::new()->create();
        $collaborator = Collaborator::factory()->create();
        $utfpr = Location::query()->where('code', 'UTFPR')->firstOrFail();

        Item::factory()->create([
            'category_id' => $category->id,
            'collaborator_id' => $collaborator->id,
            'location_id' => $utfpr->id,
            'validation' => true,
            'identification_code' => 'LOC_IX_SHOULD_HIDE',
        ]);

        $response = $this->get($this->catalogIndexUrl([
            'location_id' => 'not-a-valid-int',
        ]));

        $response->assertOk();
        $response->assertSee(__('view.catalog.items.index.none_found'), false);
        $response->assertDontSee('LOC_IX_SHOULD_HIDE', false);
    }
}
