<?php

namespace Tests\Unit\Services\Catalog;

use App\Models\Catalog\Item;
use App\Models\Catalog\ItemComponent;
use App\Models\Collaborator\Collaborator;
use App\Models\Location;
use App\Services\Catalog\ItemComponentService;
use Database\Factories\Catalog\ItemCategoryFactory;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('services')]
class ItemComponentServiceTest extends ServiceMysqlTestCase
{
    public function test_attach_contributed_components_skips_non_positive_ids(): void
    {
        $item = $this->makeItem();
        $svc = app(ItemComponentService::class);

        $svc->attachContributedComponents($item, [
            ['item_id' => 0],
            ['item_id' => -1],
        ]);

        $this->assertSame(0, ItemComponent::query()->where('item_id', $item->id)->count());
    }

    public function test_attach_contributed_components_throws_when_component_missing(): void
    {
        $item = $this->makeItem();
        $svc = app(ItemComponentService::class);

        $this->expectException(ValidationException::class);

        $svc->attachContributedComponents($item, [
            ['item_id' => 9_999_999],
        ]);
    }

    public function test_attach_contributed_components_creates_rows(): void
    {
        $parent = $this->makeItem();
        $child = $this->makeItem();

        $svc = app(ItemComponentService::class);
        $svc->attachContributedComponents($parent, [
            ['item_id' => $child->id],
        ]);

        $this->assertDatabaseHas('item_component', [
            'item_id' => $parent->id,
            'component_id' => $child->id,
            'validation' => 0,
        ]);
    }

    private function makeItem(): Item
    {
        $categoryId = ItemCategoryFactory::new()->create()->id;
        $locationId = Location::factory()->create()->id;
        $collaboratorId = Collaborator::factory()->create()->id;

        return Item::factory()->create([
            'category_id' => $categoryId,
            'location_id' => $locationId,
            'collaborator_id' => $collaboratorId,
        ]);
    }
}
