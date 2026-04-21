<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Collaborator;

use App\Enums\Collaborator\CollaboratorRole;
use App\Models\Collaborator\Collaborator;
use App\Services\Collaborator\CatalogCollaboratorVerificationService;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('services')]
class CatalogCollaboratorVerificationServiceTest extends ServiceMysqlTestCase
{
    public function test_confirm_email_verification_code_invalid_without_pending_session(): void
    {
        $svc = app(CatalogCollaboratorVerificationService::class);

        $this->assertSame(['status' => 'invalid'], $svc->confirmEmailVerificationCode(
            'nobody@example.com',
            '123456',
            'Name',
        ));
    }

    public function test_request_email_verification_code_internal_reserved(): void
    {
        $email = 'internal_' . uniqid('', false) . '@example.com';
        Collaborator::create([
            'full_name' => 'Internal',
            'email' => $email,
            'role' => CollaboratorRole::INTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);

        $svc = app(CatalogCollaboratorVerificationService::class);
        $out = $svc->requestEmailVerificationCode($email, 'Someone');

        $this->assertSame('internal_reserved', $out['status']);
    }
}
