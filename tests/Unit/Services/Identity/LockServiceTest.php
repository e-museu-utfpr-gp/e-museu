<?php

namespace Tests\Unit\Services\Identity;

use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
use App\Models\Location;
use App\Services\Identity\LockService;
use Database\Factories\Catalog\ItemCategoryFactory;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('services')]
class LockServiceTest extends ServiceMysqlTestCase
{
    public function test_resolve_subject_returns_item_and_null_status_for_known_type(): void
    {
        $categoryId = ItemCategoryFactory::new()->create()->id;
        $locationId = Location::factory()->create()->id;
        $collaboratorId = Collaborator::factory()->create()->id;

        $item = Item::factory()->create([
            'category_id' => $categoryId,
            'location_id' => $locationId,
            'collaborator_id' => $collaboratorId,
        ]);

        $svc = app(LockService::class);
        [$subject, $status] = $svc->resolveSubject(['type' => 'items', 'id' => $item->id]);

        $this->assertInstanceOf(Item::class, $subject);
        $this->assertSame($item->id, $subject->id);
        $this->assertNull($status);
    }

    public function test_resolve_subject_returns_not_found_for_missing_id(): void
    {
        $svc = app(LockService::class);
        [$subject, $status] = $svc->resolveSubject(['type' => 'items', 'id' => 9_999_999]);

        $this->assertNull($subject);
        $this->assertSame(404, $status);
    }

    public function test_resolve_subject_returns_bad_request_for_unknown_type(): void
    {
        $svc = app(LockService::class);
        [$subject, $status] = $svc->resolveSubject(['type' => 'unknown', 'id' => 1]);

        $this->assertNull($subject);
        $this->assertSame(400, $status);
    }
}
