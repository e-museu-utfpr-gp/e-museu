<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Collaborator;

use App\Enums\Collaborator\CollaboratorRole;
use App\Services\Collaborator\CollaboratorService;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('services')]
class CollaboratorServiceDatabaseTest extends ServiceMysqlTestCase
{
    public function test_create_collaborator_internal_role_auto_marks_email_verified(): void
    {
        $svc = app(CollaboratorService::class);
        $collaborator = $svc->createCollaborator([
            'full_name' => 'Internal staff',
            'email' => 'internal_staff_' . uniqid('', false) . '@example.com',
            'role' => CollaboratorRole::INTERNAL,
            'blocked' => false,
        ]);

        $this->assertNotNull($collaborator->last_email_verification_at);
    }
}
