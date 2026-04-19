<?php

namespace Tests\Unit\Services\Catalog;

use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Models\Location;
use App\Services\Catalog\ItemService;
use Database\Factories\Catalog\ItemCategoryFactory;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('services')]
class ItemServiceTest extends ServiceMysqlTestCase
{
    public function test_get_item_category_name_returns_empty_for_blank_id(): void
    {
        $svc = app(ItemService::class);

        $this->assertSame('', $svc->getItemCategoryName(null));
        $this->assertSame('', $svc->getItemCategoryName(''));
    }

    public function test_map_items_to_category_select_json_strips_location_and_sets_label(): void
    {
        $categoryId = ItemCategoryFactory::new()->create()->id;
        $location = Location::factory()->create();
        $item = Item::factory()->create([
            'category_id' => $categoryId,
            'location_id' => $location->id,
            'collaborator_id' => Collaborator::factory()->create()->id,
        ]);
        $item->load('location');

        $svc = app(ItemService::class);
        $out = $svc->mapItemsToCategorySelectJson(new Collection([$item]));

        $this->assertCount(1, $out);
        $row = $out->first();
        $this->assertIsArray($row);
        $this->assertArrayNotHasKey('location', $row);
        $this->assertArrayHasKey('location_label', $row);
    }
}
