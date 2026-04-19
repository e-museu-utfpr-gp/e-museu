<?php

namespace Tests\Unit\Services\Catalog;

use App\Models\Catalog\Extra;
use App\Models\Catalog\Item;
use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Collaborator\Collaborator;
use App\Models\Location;
use App\Services\Catalog\ExtraService;
use App\Services\Collaborator\CollaboratorService;
use Database\Factories\Catalog\ItemCategoryFactory;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('services')]
class ExtraServiceTest extends ServiceMysqlTestCase
{
    public function test_store_single_extra_returns_collaborator_invalid_when_missing(): void
    {
        $collabSvc = $this->createMock(CollaboratorService::class);
        $collabSvc->expects($this->never())->method('publicContributionCollaboratorGate');

        $svc = app(ExtraService::class);
        $out = $svc->storeSingleExtra($collabSvc, ['full_name' => 'x'], [
            'collaborator_id' => 9_999_999,
            'content_locale' => 'pt_BR',
            'item_id' => 1,
            'info' => 'text',
        ]);

        $this->assertSame('collaborator_invalid', $out['status']);
    }

    public function test_store_single_extra_returns_internal_blocked_for_internal_role(): void
    {
        $internal = Collaborator::factory()->create([
            'role' => CollaboratorRole::INTERNAL,
        ]);

        $collabSvc = $this->createMock(CollaboratorService::class);
        $collabSvc->expects($this->never())->method('publicContributionCollaboratorGate');

        $svc = app(ExtraService::class);
        $out = $svc->storeSingleExtra($collabSvc, ['full_name' => 'x'], [
            'collaborator_id' => $internal->id,
            'content_locale' => 'pt_BR',
            'item_id' => 1,
            'info' => 'text',
        ]);

        $this->assertSame('internal_blocked', $out['status']);
    }

    public function test_store_single_extra_returns_ok_and_persists_extra(): void
    {
        $categoryId = ItemCategoryFactory::new()->create()->id;
        $locationId = Location::factory()->create()->id;
        $collaborator = Collaborator::factory()->create([
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);
        $item = Item::factory()->create([
            'category_id' => $categoryId,
            'location_id' => $locationId,
            'collaborator_id' => $collaborator->id,
        ]);

        $collabSvc = $this->createMock(CollaboratorService::class);
        $collabSvc->expects($this->once())->method('publicContributionCollaboratorGate')->willReturn('ok');
        $collabSvc->expects($this->once())->method('applySubmittedFullNameAfterVerifiedContribution');

        $svc = app(ExtraService::class);
        $out = $svc->storeSingleExtra($collabSvc, [
            'full_name' => $collaborator->full_name,
            'email' => $collaborator->email,
        ], [
            'collaborator_id' => $collaborator->id,
            'content_locale' => 'pt_BR',
            'item_id' => $item->id,
            'info' => 'Extra info line.',
        ]);

        $this->assertSame('ok', $out['status']);
        $this->assertArrayHasKey('extra', $out);
        $extra = $out['extra'] ?? null;
        $this->assertInstanceOf(Extra::class, $extra);
        $this->assertDatabaseHas('extras', [
            'id' => $extra->id,
            'item_id' => $item->id,
            'collaborator_id' => $collaborator->id,
        ]);
    }
}
