<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Catalog;

use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Services\Catalog\CatalogItemViewDataService;
use Database\Factories\Catalog\ItemCategoryFactory;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('mysql')]
#[Group('services')]
final class CatalogItemViewDataServiceTest extends ServiceMysqlTestCase
{
    public function test_for_create_includes_contribution_locale_fields_and_locations(): void
    {
        $svc = app(CatalogItemViewDataService::class);
        $data = $svc->forCreate();

        $this->assertArrayHasKey('categories', $data);
        $this->assertArrayHasKey('itemCategories', $data);
        $this->assertArrayHasKey('contributionLanguages', $data);
        $this->assertArrayHasKey('defaultContributionContentLocale', $data);
        $this->assertArrayHasKey('locations', $data);
        $this->assertArrayHasKey('defaultCatalogLocationId', $data);

        $this->assertTrue($data['contributionLanguages']->isNotEmpty());
        $this->assertNotSame('', $data['defaultContributionContentLocale']);
    }

    public function test_for_show_exposes_same_contribution_locale_payload_as_for_create(): void
    {
        $category = ItemCategoryFactory::new()->create();
        $collaborator = Collaborator::factory()->create();
        $item = Item::factory()->create([
            'category_id' => $category->id,
            'collaborator_id' => $collaborator->id,
            'validation' => true,
        ]);
        $item->syncPrimaryLocaleTranslation([
            'name' => 'ViewDataSvc_' . uniqid('', false),
            'description' => 'd',
            'history' => 'h',
            'detail' => 't',
        ]);

        $svc = app(CatalogItemViewDataService::class);
        $create = $svc->forCreate();
        $show = $svc->forShow((string) $item->id);

        $this->assertSame(
            $create['contributionLanguages']->pluck('id')->all(),
            $show['contributionLanguages']->pluck('id')->all()
        );
        $this->assertSame(
            $create['defaultContributionContentLocale'],
            $show['defaultExtraContentLocale']
        );
    }

    public function test_for_index_returns_expected_keys(): void
    {
        $request = Request::create('/catalog/items', 'GET', [
            'item_category' => '',
            'search' => '',
            'order' => '1',
        ]);

        $svc = app(CatalogItemViewDataService::class);
        $data = $svc->forIndex($request);

        $this->assertArrayHasKey('items', $data);
        $this->assertArrayHasKey('categoryName', $data);
        $this->assertArrayHasKey('itemCategories', $data);
        $this->assertArrayHasKey('categories', $data);
        $this->assertArrayHasKey('locations', $data);
    }
}
