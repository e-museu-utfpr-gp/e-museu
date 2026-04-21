<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Catalog;

use App\Services\Catalog\CatalogContributionCompletionService;
use App\Services\Catalog\CatalogContributionReceivedMailService;
use App\Services\Collaborator\CollaboratorService;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('services')]
class CatalogContributionCompletionServiceTest extends TestCase
{
    public function test_after_item_null_still_clears_public_contribution_session(): void
    {
        $collaborators = $this->createMock(CollaboratorService::class);
        $collaborators->expects($this->once())->method('clearPublicContributionSessionAuth');

        $service = new CatalogContributionCompletionService(
            $collaborators,
            app(CatalogContributionReceivedMailService::class),
        );
        $service->afterItem(null, 1);
    }
}
