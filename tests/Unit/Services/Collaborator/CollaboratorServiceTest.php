<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Collaborator;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Collaborator\Collaborator;
use App\Services\Collaborator\CollaboratorService;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('services')]
class CollaboratorServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['mail.public_contribution_email_verification_enabled' => true]);
    }

    protected function tearDown(): void
    {
        session()->forget(CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY);

        parent::tearDown();
    }

    public function test_submitted_collaborator_name_matches_record_normalizes_whitespace_and_case(): void
    {
        $svc = app(CollaboratorService::class);

        $this->assertTrue($svc->submittedCollaboratorNameMatchesRecord('  John   Silva ', 'john silva'));
        $this->assertFalse($svc->submittedCollaboratorNameMatchesRecord('Maria', 'Maria Costa'));
    }

    public function test_public_contribution_session_gate_requires_session_email_match(): void
    {
        $svc = app(CollaboratorService::class);
        $collaborator = new Collaborator([
            'email' => 'a@example.com',
            'full_name' => 'X',
            'role' => CollaboratorRole::EXTERNAL,
        ]);

        $this->assertSame('email_unverified', $svc->publicContributionCollaboratorGate($collaborator, [
            'email' => 'a@example.com',
        ]));

        session()->put(CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY, [
            'email' => 'a@example.com',
            'expires_at' => now()->addHour()->getTimestamp(),
        ]);

        $this->assertSame('ok', $svc->publicContributionCollaboratorGate($collaborator, [
            'email' => 'a@example.com',
        ]));
    }
}
