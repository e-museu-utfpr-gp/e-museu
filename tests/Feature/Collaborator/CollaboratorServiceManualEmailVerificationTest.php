<?php

declare(strict_types=1);

namespace Tests\Feature\Collaborator;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Collaborator\Collaborator;
use App\Services\Collaborator\CollaboratorService;
use Database\Factories\Collaborator\CollaboratorFactory;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
class CollaboratorServiceManualEmailVerificationTest extends AbstractMysqlRefreshDatabaseTestCase
{
    public function test_create_external_persists_manual_last_email_verification_at(): void
    {
        $service = app(CollaboratorService::class);
        $collaborator = $service->createCollaborator([
            'full_name' => 'Test User',
            'email' => 'manual-verify-create@example.com',
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => '2024-06-15 14:30:00',
        ]);

        $collaborator->refresh();
        $this->assertTrue($collaborator->hasVerifiedEmail());
        $verifiedAt = $collaborator->last_email_verification_at;
        $this->assertNotNull($verifiedAt);
        $this->assertSame('2024-06-15', $verifiedAt->format('Y-m-d'));
    }

    public function test_create_internal_respects_manual_last_email_verification_at(): void
    {
        $service = app(CollaboratorService::class);
        $collaborator = $service->createCollaborator([
            'full_name' => 'Internal Staff',
            'email' => 'internal-manual-verify@example.com',
            'role' => CollaboratorRole::INTERNAL,
            'blocked' => false,
            'last_email_verification_at' => '2023-11-20 08:00:00',
        ]);

        $collaborator->refresh();
        $this->assertTrue($collaborator->hasVerifiedEmail());
        $verifiedAt = $collaborator->last_email_verification_at;
        $this->assertNotNull($verifiedAt);
        $this->assertSame('2023-11-20', $verifiedAt->format('Y-m-d'));
    }

    public function test_update_external_email_change_keeps_verification_when_admin_sets_timestamp(): void
    {
        /** @var Collaborator $collaborator */
        $collaborator = CollaboratorFactory::new()->create([
            'email' => 'old@example.com',
            'role' => CollaboratorRole::EXTERNAL,
            'last_email_verification_at' => now()->subDay(),
        ]);

        $service = app(CollaboratorService::class);
        $service->updateCollaborator($collaborator, [
            'full_name' => $collaborator->full_name,
            'email' => 'new@example.com',
            'role' => CollaboratorRole::EXTERNAL->value,
            'blocked' => false,
            'last_email_verification_at' => '2025-01-10 09:00:00',
        ]);

        $collaborator->refresh();
        $this->assertSame('new@example.com', $collaborator->email);
        $this->assertTrue($collaborator->hasVerifiedEmail());
        $verifiedAt = $collaborator->last_email_verification_at;
        $this->assertNotNull($verifiedAt);
        $this->assertSame('2025-01-10', $verifiedAt->format('Y-m-d'));
    }

    public function test_update_external_email_change_clears_verification_without_admin_timestamp(): void
    {
        /** @var Collaborator $collaborator */
        $collaborator = CollaboratorFactory::new()->create([
            'email' => 'old2@example.com',
            'role' => CollaboratorRole::EXTERNAL,
            'last_email_verification_at' => now()->subDay(),
        ]);

        $service = app(CollaboratorService::class);
        $service->updateCollaborator($collaborator, [
            'full_name' => $collaborator->full_name,
            'email' => 'new2@example.com',
            'role' => CollaboratorRole::EXTERNAL->value,
            'blocked' => false,
        ]);

        $collaborator->refresh();
        $this->assertSame('new2@example.com', $collaborator->email);
        $this->assertFalse($collaborator->hasVerifiedEmail());
    }
}
