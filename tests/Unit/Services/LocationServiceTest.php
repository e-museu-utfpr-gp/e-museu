<?php

namespace Tests\Unit\Services;

use App\Services\LocationService;
use PHPUnit\Framework\Attributes\Group;

#[Group('services')]
class LocationServiceTest extends ServiceMysqlTestCase
{
    public function test_ordered_for_forms_returns_collection_ordered_by_id(): void
    {
        $svc = app(LocationService::class);
        $locations = $svc->orderedForForms();

        $this->assertTrue($locations->isNotEmpty());
        $ids = $locations->pluck('id')->all();
        $sorted = $ids;
        sort($sorted, SORT_NUMERIC);
        $this->assertSame($sorted, $ids);
    }

    public function test_for_item_create_forms_includes_default_catalog_location_id_key(): void
    {
        $svc = app(LocationService::class);
        $payload = $svc->forItemCreateForms();

        $this->assertArrayHasKey('locations', $payload);
        $this->assertArrayHasKey('defaultCatalogLocationId', $payload);
    }
}
